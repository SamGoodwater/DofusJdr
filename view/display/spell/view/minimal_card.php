    <?php 
     $list_name_files = [];
    foreach(Spell::FILES as $key => $file) {
        $list_name_files[] = $key;
    } ?>
<div 
    class="minimal-card" 
    style="width: 18rem;"
    data-prop-object="spell"
    data-prop-id="{{id}}"
    data-prop-uniqid="{{uniqid}}"
    data-prop-timestamp-add="{{timestamp_add}}"
    data-prop-timestamp-updated="{{timestamp_updated}}"
    data-prop-usable="{{usable}}"
    data-prop-list-name-files="<?= json_encode($list_name_files); ?>"
    <?php if(!empty($list_name_files)){
        foreach($list_name_files as $name_file) {
            echo 'data-prop-file-'.$name_file.'="'.(isset($name_file) ? $name_file : '').'"';
        }
    } ?>
    data-prop-name="{{name}}"
    data-prop-description="{{description}}"
    data-prop-effect="{{effect}}"
    data-prop-effect-array="{{effect_array}}"
    data-prop-level="{{level}}"
    data-prop-po="{{po}}"
    data-prop-po-editable="{{po_editable}}"
    data-prop-area="{{area}}"
    data-prop-pa="{{pa}}"
    data-prop-type="{{type}}"
    data-prop-cast-per-turn="{{cast_per_turn}}"
    data-prop-cast-per-target="{{cast_per_target}}"
    data-prop-sight-line="{{sight_line}}"
    data-prop-number-between-two-cast="{{number_between_two_cast}}"
    data-prop-element="{{element}}"
    data-prop-category="{{category}}"
    data-prop-is-magic="{{is_magic}}"
    data-prop-powerful="{{powerful}}" 
>
    <img data-prop="file[logo]" data-location="src" data-format="none" src="{{file['logo']}}" class="minimal-card__img" alt="...">
    <div class="minimal-card__header">
        <h5 data-prop="name" data-location="content" data-format="none" class="minimal-card__header__title">{{name}}</h5>
    </div>
    <div class="minimal-card__body">
        <div class="minimal-card__body__resume">>
            <p data-prop="level" data-location="content" data-format="badge" >{{level}}</p>
            <p data-prop="category" data-location="content" data-format="badge" >{{category}}</p>
            <p data-prop="type" data-location="content" data-format="badge" >{{type}}</p>
            <p data-prop="element" data-location="content" data-format="badge" >{{element}}</p>
        </div>
        <div class="minimal-card__body__resume">
            <p data-prop="po" data-location="content" data-format="icon" >{{po}}</p>
            <p data-prop="sight_line" data-location="content" data-format="icon" >{{sight_line}}</p>
            <p data-prop="cast_per_turn" data-location="content" data-format="icon" >{{cast_per_turn}}</p>
            <p data-prop="cast_per_target" data-location="content" data-format="icon" >{{cast_per_target}}</p>
            <p data-prop="number_between_two_cast" data-location="content" data-format="icon" >{{number_between_two_cast}}</p>
        </div>
        <div class="minimal-card__body__resume">
            <p data-prop="pa" data-location="content" data-format="icon" >{{pa}}</p>
            <p data-prop="area" data-location="content" data-format="icon" >{{area}}</p>
            <p data-prop="is_magic" data-location="content" data-format="icon" >{{is_magic}}</p>
            <p data-prop="powerful" data-location="content" data-format="icon" >{{powerful}}</p>
        </div>
    </div>
    <div class="minimal-card__footer" data-type='dropdown' data-expanded="false">
        <button class="minimal-card__footer__btn">Plus d'informations</button>
        <div> 
            <p data-prop="effect" data-location="content" data-format="none" >{{effect}}</p>
            <p data-prop="description" data-location="content" data-format="none" >{{description}}</p>
            <p data-prop="invocation" data-location="content" data-format="list" class="item-divider-main"></p>
            <div>{{invoction}}</div>
        </div>
    </div>
</div>