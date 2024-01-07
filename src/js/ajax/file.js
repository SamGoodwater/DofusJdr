class File {

    /* 
            <div id="fileupload" class="fileupload m-2">
                <form 
                    data-url="index.php?c=page&a=upload&uniqid=<?=$template_vars['uniqid']?>" 
                    data-viewimgpath="medias/page/"
                    data-dropable="true" 
                    data-dropzone="#collapse"
                    accept=".<?=implode(",.", FileManager::getListeExtention(FileManager::FORMAT_IMG, FileManager::FORMAT_AUDIO, FileManager::FORMAT_VIDEO, FileManager::FORMAT_PDF, FileManager::FORMAT_WORD, FileManager::FORMAT_TABLEUR, FileManager::FORMAT_POWERPOINT))?>"
                    capture="environnement"> 
                    <p>Ajouter un fichier à cette page</p>
                    <input class="file-input form-control form-control-sm" name="file" type="file" hidden>
                </form>
                <section class="progress-area"></section>
                <section class="uploaded-area"></section>
            </div>
            <p class="text-main-l-1 size-0-8 mt-2"><span>Attention, la section est créé dès que le fichier est chargé. Il n'y a pas besoin de cliquer sur Ajouter</span></p>
            <script>
                File.loadFileUpload('#fileupload');
            </script>   
    */

    static loadFileUpload(element) {
        let fileInput = document.querySelector(element + " .file-input"),
        form = document.querySelector(element + " form");
        form.addEventListener("click", () =>{
            fileInput.click();
        });
        let isDropable = true; // Si on peut droper un fichier (défaut true)
        if(form.dataset.dropable != "true") {isDropable = false;}

        if(isDropable) {
            let dropZone = document.querySelector(form.dataset.dropzone);
            if(!dropZone){dropZone = form;} // Si la dropzone n'existe pas, alors utiliser la form comme dropzone

            dropZone.ondragover = function () { this.className = 'dragable'; return false; };
            dropZone.ondragleave = function () { dropZone.classList.remove('dragable'); return false; };
            dropZone.ondrop = function(e) {
                dropZone.classList.remove('dragable');
                e.preventDefault();
                let files_list = e.dataTransfer.files;
                const dT = new DataTransfer();
                files_list.forEach(file => {
                    dT.items.add(file);
                });
                fileInput.files = dT.files;
                fileInput.files.forEach(file => {
                    if(file){
                        let fileName = file.name; //getting file name
                        if(fileName.length >= 12){ //if file name length is greater than 12 then split it and add ...
                            let splitName = fileName.split('.');
                            fileName = splitName[0].substring(0, 13) + "... ." + splitName[1];
                        }
                        File.uploadFile(fileName, element); //calling uploadFile with passing file name as an argument
                    }
                });
            };
        }

        fileInput.onchange = ({target})=>{
            let file = target.files[0]; //getting file [0] this means if user has selected multiple files then get first one only
            if(file){
                let fileName = file.name; //getting file name
                if(fileName.length >= 12){ //if file name length is greater than 12 then split it and add ...
                    let splitName = fileName.split('.');
                    fileName = splitName[0].substring(0, 13) + "... ." + splitName[1];
                }

                document.querySelectorAll(element + " form input").forEach(function(input, index){
                    input = $(input);
                    if($(".modal-dialog #"+ input.attr("name")).length > 0){
                        input.val($(".modal-dialog #"+ input.attr("name")).val()); 
                    }
                });

                File.uploadFile(fileName, element); //calling uploadFile with passing file name as an argument
            }
        }
    }

    // file upload function
    static uploadFile(name, element){
        let form = document.querySelector(element + " form"),
        progressArea = document.querySelector(element + " .progress-area"),
        uploadedArea = document.querySelector(element + " .uploaded-area"),
        input = document.querySelector(element + " .file-input");
        let url = form.dataset.url;

        let xhr = new XMLHttpRequest(); //creating new xhr object (AJAX)
        xhr.open("POST", url); //sending post request to the specified URL
        xhr.responseType = 'json';
        xhr.upload.addEventListener("progress", ({loaded, total}) =>{ //file uploading progress event
            let fileLoaded = Math.floor((loaded / total) * 100);  //getting percentage of loaded file size
            let fileTotal = Math.floor(total / 1000); //gettting total file size in KB from bytes
            let fileSize;
            // if file size is less than 1024 then add only KB else convert this KB into MB
            (fileTotal < 1024) ? fileSize = fileTotal + " KB" : fileSize = (loaded / (1024*1024)).toFixed(2) + " MB";
            
            let progressHTML = `<li class="row">
                                    <i class='fa-solid fa-file-alt'></i>
                                    <div class="content">
                                        <div class="details">
                                            <span class="name">${name} • Téléchargement</span>
                                            <span class="percent">${fileLoaded}%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress" style="width: ${fileLoaded}%"></div>
                                        </div>
                                    </div>
                                </li>`;

            // uploadedArea.innerHTML = ""; //uncomment this line if you don't want to show upload history
            uploadedArea.classList.add("onprogress");
                let thumbail = "<i class='fa-solid fa-file-alt'></i>";
                // Affichage de la mignature
                let file = input.files[0];
                let regex = /(image|jpg|jpeg|gif|png|svg|bmp|tif|tiff|raw|ico)/ig;
                let path = form.dataset.viewimgpath + file.name;
                if(regex.test(file.type)) {
                    thumbail = "<img src='"+path+"' alt='Fichier téléchargé' class='img-fluid' width=30px>";
                }

                progressArea.innerHTML = progressHTML;
                if(loaded == total){
                        progressArea.innerHTML = "";
                        let uploadedHTML = `<li class="row">
                                                <div class="content upload">
                                                    `+thumbail+`
                                                    <div class="details">
                                                        <span class="name">${name} • Téléchargé</span>
                                                        <span class="size">${fileSize}</span>
                                                    </div>
                                                </div>
                                                <i class="fa-solid fa-check"></i>
                                            </li>`;

                    uploadedArea.classList.remove("onprogress");
                    // uploadedArea.innerHTML = uploadedHTML; //uncomment this line if you don't want to show upload history

                    uploadedArea.insertAdjacentHTML("afterbegin", uploadedHTML); //remove this line if you don't want to show upload history
                }
        });
        let data = new FormData(form); //FormData is an object to easily send form data
        xhr.send(data); //sending form data

        xhr.onload = function() {
            let data = xhr.response;
            if(data.script != ""){
                $('body').append("<script>"+data.script+"</script>");
            }
            if(data.state){
                MsgAlert("Le fichier est uploadé.", '', "green" , 3000);
            } else {
                MsgAlert("Impossible d'uploader le fichier", 'Erreur : ' + data.error, "danger" , 4000);
            }
        };

    }

    static remove(path){
        let URL = 'index.php?c=file&a=remove';
    
        if (confirm("Etes vous sûr de vouloir supprimer définitivement ce fichier ?")) {
            $.post(URL,
                {
                    path:path
                },
                function(data, status)
                {
                    if(data.script != ""){
                        $('body').append("<script>"+data.script+"</script>");
                    }
                    if(data.state){
                        MsgAlert("Suppression du fichier", "Le fichier a bien été supprimé.", "green" , 3000);
                    } else {
                        $('#display_error').text(data['error']);
                        MsgAlert("Echec de la suppresion", 'Erreur : ' + data.error, "red-d-3" , 4000);
                    }
                },
                "json"
            ); 
        }
    }

    static removeThumbnail(path){
        let URL = 'index.php?c=file&a=removeThumbnail';
    
        if (confirm("Etes vous sûr de supprimer et recréer les miniatures pour cette image ?")) {
            $.post(URL,
                {
                    path:path
                },
                function(data, status)
                {
                    if(data.script != ""){
                        $('body').append("<script>"+data.script+"</script>");
                    }
                    if(data.return){
                        MsgAlert("Mise à jour des miniatures", "Les miniatures ont bien été supprimé.", "green-d-3" , 3000);
                    } else {
                        MsgAlert("Echec de la mise à jour des miniatures", 'Erreur : ' + data.error, "red-d-3" , 4000);
                    }
                },
                "json"
            ); 
        }
    
    }
    static addThumbnail(path){
        let URL = 'index.php?c=file&a=addThumbnail';
        $.post(URL,
            {
                path:path
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.return){
                    MsgAlert("Mise à jour des miniatures", "Les miniatures ont bien été ajouté.", "green-d-3" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour des miniatures", 'Erreur : ' + data.error, "red-d-3" , 4000);
                }
            },
            "json"
        ); 
    }
}