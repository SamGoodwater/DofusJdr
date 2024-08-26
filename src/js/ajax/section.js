class Section extends Controller{
    static MODEL_NAME = "section";

    static add(url_name)
    {
        let URL = 'index.php?c=section&a=add';
        let type = $('#modal #type').val();
        let title = $('#modal #title').val();
        let option = "";

        let optionSelect = $('#modal #type').find("option:selected");
        let optionRefData = optionSelect.data("ref_stock_data_option");
        console.log(optionRefData);
        if(optionRefData != undefined && optionRefData != null && optionRefData != ''){
            console.log($(optionRefData));
            if($(optionRefData).length > 0){
                option = $(optionRefData).val();
                console.log(option);
            } else {
                MsgAlert("Echec d'option'", "Impossible d'accéder aux valeurs des options", "danger" , 4000);
            }
        }
        
        $.post(URL,
            {
                type:type,
                title:title,
                url_name:url_name,
                option:option
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Ajout de la section", 'La section ' + title + ' a bien été ajouté.', "green" , 2000);
                    $("#modal").modal("hide");
                    $('#modal #type').val("");
                    $('#modal #title').val("");
                    Page.show(url_name);
                } else {
                    MsgAlert("Echec de l'ajout", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }

    static upload(url_name){
        let type = $('#modal #type').val();
        let title = $('#modal #title').val();
        let url = "index.php?c=section&a=upload&url_name="+url_name+"&title="+title+"&type="+type;

        $('#fileupload').fileupload({
            url:url,
            dropZone: '#optionImage',
            dataType: 'json',
            add: function (e, data) {
                data.submit();
                $('#progressBar').removeClass('bg-info').removeClass('bg-success').removeClass('bg-danger');
                let fileSize = data.originalFiles[0]["size"];
                if(fileSize > 15000000) {
                    $('#progressBar').attr("aria-valuenow",100);
                    $('#progressBar').css('width', '100%');
                    $('#progressBar').text("Taille du fichier trop grand");
                    $('#progressBar').addClass('bg-danger');
                    MsgAlert("Erreur de taille", "La taille du fichier " +fileSize+ "o n'est pas correcte. Il ne doit pas dépassé 15Mo.", "danger" , 3000);
                } else {
                    $('#progressBar').attr("aria-valuenow",100);
                    $('#progressBar').css('width', '100%');
                    $('#progressBar').text("Envoyé");
                    $('#progressBar').addClass('bg-info');
                }
            },
        }).on('fileuploaddone', function(e, data){
            if(data.result.state){
                $('#progressBar').attr("aria-valuenow",100);
                $('#progressBar').css('width', '100%');
                $('#progressBar').text("Fichier reçu");
                $('#progressBar').removeClass('bg-info').addClass('bg-success');
                MsgAlert("Ajout de la section", 'La section ' + title + ' a bien été ajouté.', "green" , 2000);
                $("#modal").modal("hide");
                $('#modal #type').val("");
                $('#modal #title').val("");
                Page.show(url_name);
            } else {
                MsgAlert("Echec de l'ajout du fichier", 'Erreur : ' + data.result.value, "danger" , 4000);
            }

        }).on('fileuploadprogressall', function(e, data){
            $('#progressBar').removeClass('bg-danger').removeClass('bg-info').removeClass('bg-success');
            let progress = parseInt(data.loaded / data.total * 100);
            $('#progressBar').attr("aria-valuenow",progress);
            $('#progressBar').css('width', progress+'%');
            $('#progressBar').text(progress+'%');
        });
    }
    
    static update(uniqid, input, type, value_type = IS_INPUT, fct = ""){
        let URL = 'index.php?c=section&a=update';
        let value = 0;
    
        switch (value_type) {
            case IS_INPUT:
                let inp = $(input);
                if(inp.attr("required") && inp.val() == ""){
                    inp.addClass("is-invalid").removeClass('is-valid');
                    MsgAlert("Le champs est obligatoire.", '', "error" , 3000);
                    return false;
                } else {
                    inp.addClass("is-valid").removeClass('is-invalid');
                    value = inp.val();
                }
            break;
            case IS_VALUE:
                value = input;
            break;
            case IS_CHECKBOX:
                if ($(input).is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            break;
            case IS_CKEDITOR:
                value = CKEDITOR.instances[type+id].getData();
            break;
            case IS_PATH_FILE:
                if(file_select.extention != "dir" && file_select.path != ""){
                    value = file_select.dirname + file_select.name;
                    $("#showFile_"+type+" a").attr('href', value);
                    $("#showFile_"+type+" a div").css('background-image', "url('"+value+"')");
                } else {
                    alert('Aucun fichier sélectionné');
                }
            break;
            default:
                MsgAlert("Aucun type de valeur spécifié", '', "error" , 3000);
                return false;
        }
    
        if(fct !=""){
            value = fct(value);
            if(value == '***error***'){
                MsgAlert("Echec de la mise à jour", "Erreur d'éxécuttion de la fonction", "danger" , 4000);
                return false;
            }
            $(input).val(value);
        }
    
        $.post(URL,
            {
                uniqid:uniqid,
                value:value,
                type:type
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script+"</script>");
                }
                if(data.state){
                    MsgAlert("Mise à jour de la section", '', "green" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    static remove(uniqid){
        let URL = 'index.php?c=section&a=remove';
    
        if (confirm("Etes vous sûr de vouloir supprimer définitivement cette section ?")) {
            $.post(URL,
                {
                    uniqid:uniqid
                },
                function(data, status)
                {
                    if(data.script != ""){
                        $('body').append("<script>"+data.script+"</script>");
                    }
                    if(data.state){
                        $("#section"+uniqid).html("");
                        MsgAlert("Suppression de la section", 'La section a bien été supprimé.', "green" , 3000);
                    } else {
                        MsgAlert("Echec de la suppresion", 'Erreur : ' + data.error, "danger" , 4000);
                    }
                },
                "json"
            ); 
        }
    
    }

    static showEdit(uniqid){

        if( $('#section'+uniqid).data("editing") == "true"){
            console.log("Fermeture du mode édition de la section");
            $('#section'+uniqid).data("editing", "false");

            $('#section'+uniqid+" .trash").hide("slow");
            $('#section'+uniqid+" #saveCkeditor").hide("slow");

            let title = $("#section"+uniqid+" h1 input").val();
            $("#section"+uniqid+" h1").html(title);

            Section.update(uniqid, CKEDITOR5['content'+uniqid].getData(), 'content', IS_VALUE);
            CKEDITOR5['content'+uniqid].destroy();
            
        } else {
            console.log("Mode édition de la section");
            $('#section'+uniqid).data("editing", "true");

            $('#section'+uniqid+" .trash").show("slow");
            $('#section'+uniqid+" #saveCkeditor").show("slow");

            let title_text = $("#section"+uniqid+" h1").text();
            let input = "<input class='form-control form-control-main-l-3-focus' onchange=\"Section.update('"+uniqid+"', this, 'title');\" placeholder='titre de la section' type='text' value=''>";
            $("#section"+uniqid+" h1").html(input);
            $("#section"+uniqid+" h1").find("input").val(title_text);

            ClassicEditor
                .create( document.querySelector('#content'+uniqid), { 
                    autosave: {
                        waitingTime: 10000, // in ms
                        save(editor) {
                            Section.update(uniqid, editor.getData(), 'content', IS_VALUE);
                        }
                    },
                    toolbar: {
                        items: [
                            'undo',
                            'redo',
                            '|',
                            'heading',
                            'alignment',
                            'fontSize',
                            'fontFamily',
                            '|',
                            'fontColor',
                            'fontBackgroundColor',
                            'highlight',
                            '|',
                            'link',
                            'insertTable',
                            'imageInsert',
                            '|',
                            'bold',
                            'italic',
                            'strikethrough',
                            'underline',
                            'subscript',
                            'superscript',
                            '|',
                            'bulletedList',
                            'numberedList',
                            'todoList',
                            '|',
                            'outdent',
                            'indent',
                            '|',
                            'specialCharacters',
                            'imageUpload',
                            '|',
                            'mediaEmbed',
                            'horizontalLine',
                            'blockQuote',
                            '|',
                            'removeFormat',
                            'htmlEmbed',
                            'code',
                            'sourceEditing',
                            'findAndReplace'
                        ]
                    },
                    language: 'fr',
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'imageStyle:inline',
                            'imageStyle:block',
                            'imageStyle:side',
                            'linkImage'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells',
                            'tableCellProperties',
                            'tableProperties'
                        ]
                    },
                    removePlugins: [
                        "htmlwriter"
                    ]
                } )
                .then( newEditor => {
                    CKEDITOR5['content'+uniqid] = newEditor;
                    $(".ck-file-dialog-button button").off("click");
                    $(".ck-file-dialog-button button").unbind('click');
                } )
                .catch( error => {
                    console.error( 'Oops, something went wrong!' );
                    console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                    console.warn( 'Build id: 2jnb9i33ls8a-f2lnu5o5jd3g' );
                    console.error( error );
                } );
        }
    }

    static updateOrder_num(){
        $('.section__container').each(function(index, value) {
            Section.update($(this).data("uniqid"),index,'order_num', IS_VALUE);
        });
    }

    static getVisual(uniqid, show_modal = true){
        let URL = 'index.php?c=section&a=getVisual';
        $.post(URL,
            {
                uniqid:uniqid,
            },
            function(data, status)
            {
                if(data.script != ""){
                    $('body').append("<script>"+data.script +   "</script>");
                }
                Page.build({
                    target : "modal", 
                    title : data.title,
                    content : data.modql.html,
                    size : data.size, 
                    show : show_modal
                });
            },
            "json"
        ); 
    }

}