<?php 
    // Obligatoire
    if(!isset($class_name)) {throw new Exception("class_name is not set");}else{if(!class_exists($class_name)) {throw new Exception("class_name is not set");}}
    if(!isset($uniqid)) {throw new Exception("uniqid is not set");}else{if(is_object($uniqid)) {throw new Exception("uniqid is not set");}}
    if(!isset($input_name)) {throw new Exception("input_name is not set");}else{if(!is_string($input_name)) {throw new Exception("input_name is not set");}}
    if(!isset($id)) { $id = "ckeditor_".$uniqid;}else{if(!is_string($id)) {$id = "ckeditor_".$uniqid;}}

    // Conseillé
    if(!isset($label)) {$label = "";}else{if(!is_string($label) && !is_numeric($content)) {$label = "";}}
    if(!isset($value)) { $value = "";}else{if(!is_string($value) && !is_numeric($content)) {$value = "";}}

    // Optionnel - valeur par défault ok
    if(!isset($comment)) { $comment = "";}else{if(!is_string($comment) && !is_numeric($content)) {$comment = "";}}
    if(!isset($color)) { $color = "main";}else{if(!Style::isValidColor($color)) {$color = "main";}}
    if(!isset($class)) { $class = "";}else{if(!is_string($class)) {$class = "";}}
    if(!isset($data)) { $data = "";}else{if(!is_string($data)) {$data = "";}}
    if(!isset($css)) { $css = "";}else{if(!is_string($css)) {$css = "";}}
?>

<div class="<?=$class?>" <?=$data?> style="<?=$css?>">
    <p class="text-<?=$color?>-d-2"><?=$label?></p>
    <div  id="<?=$id?>"><?=html_entity_decode($value)?></div>
    <a class='p-1 back-grey-l-2-hover' onclick="<?=ucfirst($class_name)?>.update('<?=$uniqid?>', CKEDITOR5['<?=$id?>'].getData(), '<?=$input_name?>', <?=Controller::IS_VALUE?>)"><small><i class="fa-solid fa-save"></i> - N'oublier pas d'enregistrer régulièrement</small></a>
    <?php if(!empty($comment)){ ?>
        <span class="size-0-8 text-grey"><?=$comment?></span>
    <?php } ?>
</div>
<script>
    ClassicEditor.create( document.querySelector('#<?=$id?>'), { 
            autosave: {
                waitingTime: 10000, // in ms
                save(editor) {
                    <?=ucfirst($class_name)?>.update('<?=$uniqid?>', editor.getData(), '<?=$input_name?>', IS_VALUE);
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
            licenseKey: '',  
        } )
        .then( newEditor => {
            CKEDITOR5['<?=$id?>'] = newEditor;
            $(".ck-file-dialog-button button").off("click");
            $(".ck-file-dialog-button button").unbind('click');
        } )
        .catch( error => {
            console.error( 'Oops, something went wrong!' );
            console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
            console.warn( 'Build id: 2jnb9i33ls8a-f2lnu5o5jd3g' );
            console.error( error );
        } );
</script>
