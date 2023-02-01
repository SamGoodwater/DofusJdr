class Section {

    static add(url_name)
    {
        var URL = 'index.php?c=section&a=add';
        var type = $('#modal #type').val();
        var title = $('#modal #title').val();
        var option = "";
        switch (type) {
            case "item.php":
                var checkboxes = document.querySelectorAll('#option.item input[type="checkbox"]');
                for (var checkbox of checkboxes) {
                    if(checkbox.checked){
                        option += checkbox.value + "|";
                    }
                    checkbox.checked = false;
                }
                option = option.substring(0, option.length - 1);
            break;
            default:
                option = $("#modal #option").val();
            break;
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
                if(data['return'] == 'success'){
                    MsgAlert("Ajout de la section", 'La section ' + title + ' a bien été ajouté.', "success" , 2000);
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
        var type = $('#modal #type').val();
        var title = $('#modal #title').val();
        var url = "index.php?c=section&a=upload&url_name="+url_name+"&title="+title+"&type="+type;

        $('#fileupload').fileupload({
            url:url,
            dropZone: '#optionImage',
            dataType: 'json',
            add: function (e, data) {
                data.submit();
                $('#progressBar').removeClass('bg-info').removeClass('bg-success').removeClass('bg-danger');
                var fileSize = data.originalFiles[0]["size"];
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
            if(data.result.state == 'success'){
                $('#progressBar').attr("aria-valuenow",100);
                $('#progressBar').css('width', '100%');
                $('#progressBar').text("Fichier reçu");
                $('#progressBar').removeClass('bg-info').addClass('bg-success');
                MsgAlert("Ajout de la section", 'La section ' + title + ' a bien été ajouté.', "success" , 2000);
                $("#modal").modal("hide");
                $('#modal #type').val("");
                $('#modal #title').val("");
                Page.show(url_name);
            } else {
                MsgAlert("Echec de l'ajout du fichier", 'Erreur : ' + data.result.value, "danger" , 4000);
            }

        }).on('fileuploadprogressall', function(e, data){
            $('#progressBar').removeClass('bg-danger').removeClass('bg-info').removeClass('bg-success');
            var progress = parseInt(data.loaded / data.total * 100);
            $('#progressBar').attr("aria-valuenow",progress);
            $('#progressBar').css('width', progress+'%');
            $('#progressBar').text(progress+'%');
        });
    }
    
    static update(uniqid, input, type, value_type = IS_INPUT, fct = ""){
        var URL = 'index.php?c=section&a=update';
        var value = 0;
    
        switch (value_type) {
            case IS_INPUT:
                var inp = $(input);
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
                if(data['script'] != ""){
                    $('body').append("<script>"+data['script']+"</script>");
                }
                if(data['return'] == "success"){
                    MsgAlert("Mise à jour de la section", '', "success" , 3000);
                } else {
                    MsgAlert("Echec de la mise à jour", 'Erreur : ' + data['error'], "danger" , 4000);
                }
            },
            "json"
        ); 
    }
    
    static remove(uniqid){
        var URL = 'index.php?c=section&a=remove';
    
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
                    if(data.return == 'success'){
                        $("#section"+uniqid).html("");
                        MsgAlert("Suppression de la section", 'La section a bien été supprimé.', "success" , 3000);
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

            var title = $("#section"+uniqid+" h3 input").val();
            $("#section"+uniqid+" h3").html(title);

            Section.update(uniqid, CKEDITOR5['content'+uniqid].getData(), 'content', IS_VALUE);
            CKEDITOR5['content'+uniqid].destroy();
            
        } else {
            console.log("Mode édition de la section");
            $('#section'+uniqid).data("editing", "true");

            $('#section'+uniqid+" .trash").show("slow");
            $('#section'+uniqid+" #saveCkeditor").show("slow");

            var title = "<input class='form-control form-control-main-l-3-focus' onchange=\"Section.update('"+uniqid+"', this, 'title');\" placeholder='titre de la section' type='text' value='"+$("#section"+uniqid+" h3").text()+"'>";
            $("#section"+uniqid+" h3").html(title);

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
        $('.sectionselector').each(function(index, value) {
            Section.update($(this).data("uniqid"),index,'order_num', IS_VALUE);
        });
    }

    static getVisual(uniqid, show_modal = true){
        var URL = 'index.php?c=section&a=getVisual';
        $.post(URL,
            {
                uniqid:uniqid,
                is_flush:true
            },
            function(data, status)
            {
                console.log(data);
                if(data.script != ""){
                    $('body').append("<script>"+data.script +   "</script>");
                }
                Page.build(Page.RESPONSIVE, data.title,  data.modal.html, data.size, show_modal);
            },
            "json"
        ); 
    }

}