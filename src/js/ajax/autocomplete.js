/* EXEMPLE DE SCRIPT

<h6 class="mt-1">Ajouter des équipements</h6>
<div class="d-flex flex-row justify-content-evenly align-items-baseline mb-3 position-relative">
    <div class="form-floating w-100">
        <input  type="text" 
                data-url = "index.php?c=search&a=search"
                data-search_in = <?=ControllerModule::SEARCH_IN_SPELL?>
                data-minlenght = 3
                data-parameter = "<?=$this->getUniqid()?>"
                data-action = <?=ControllerModule::SEARCH_DONE_ADD_SPELL_TO_MOB?>
                data-limit = 10
                data-only_usable = true
                class="form-control" 
                id="addItem<?=$this->getUniqid()?>" 
                placeholder="Rechercher un équipement">
        <label for="addItem<?=$this->getUniqid()?>">Rechercher un équipement</label>
    </div>
    <span id="search-sign"></span>
</div>
<script>autocomplete_load("#addItem<?=$this->getUniqid()?>");</script>
*/

const SEARCH_TRACK_ALL = 0;
const SEARCH_TRACK_PAGE = 10;

const SEARCH_ACTION_REDIRECTION = 1;
const SEARCH_ACTION_ADD = 2;

function autocomplete_load(input, display_error = false) {
    let error = "";
    let input_ = $(input);
    let sign = input_.parent().parent().find("#search-sign");
    sign.html("");
    if(input_.data("url") != 'undefined'){
        url = input_.data("url");
    } else {
        MsgAlert("Aucun url trouvé", '' + data['error'], "red-d-3" , 2000);
        return "";
    }
    if(input_.data("parameter") != 'undefined'){
        url += "&parameter=" + input_.data("parameter");
    }
    if(input_.data("search_in") != 'undefined'){
        url += "&search_in=" + input_.data("search_in");
    }
    if(input_.data("action") != 'undefined'){
        url += "&action=" + input_.data("action");
    }
    if(input_.data("limit") != 'undefined'){
        url += "&limit=" + input_.data("limit");
    }
    if(input_.data("only_usable") != 'undefined'){
        url += "&only_usable=" + input_.data("only_usable");
    }
    if(input_.data("minlenght") != 'undefined'){
        minLength = input_.data("minlenght");
    } else {
        minLength = 3;
    }

    input_.autocomplete({
        source: url,
        minLength: minLength,
        search: function( event, ui ){
            sign.html("<div class='spinner-border' role='status'><span class='visually-hidden'>Loading...</span></div>");
        },
        response: function( event, ui ){
            sign.html("");
        },
        select: function( event, ui ) {
            input_.val('');
        }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
        if(item.error != 'undefined'){error = item.error}else{error = false;}
        if(display_error || error != true){
            return $( "<li>" )
            .append( "<div>" + item.visual + "</div>" )
            .appendTo( ul );
        }
    };
}