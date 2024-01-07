
function minmaxDice(){
    let number_dice = $("#number_dice").val();
    if(number_dice == 0 || number_dice == ""){
        number_dice = 1;
    }
    let type_dice = $("#type_dice").val();
    if(type_dice == 0 || type_dice == ""){
        type_dice = 6;
    }
    let add_int = $("#add_int").val();
    if(add_int == 0 || add_int == ""){
        add_int = 0;
    }
    let min = 0;
    let max = 0;
    for(let i = 0; i < number_dice; i++){
        max += parseInt(type_dice);
        min += parseInt(1);
    }
    min += parseInt(add_int);
    max += parseInt(add_int);
    $("#min-max").html("Minimum : " + min + " | Maximum : " + max);

}
function rollDice() {
    let number_dice = $("#number_dice").val();
    if(number_dice == 0 || number_dice == ""){
        number_dice = 1;
    }
    let type_dice = $("#type_dice").val();
    if(type_dice == 0 || type_dice == ""){
        type_dice = 6;
    }
    let add_int = $("#add_int").val();
    if(add_int == 0 || add_int == ""){
        add_int = 0;
    }
    let result = 0;
    for(let i = 0; i < number_dice; i++){
        result += Math.floor(Math.random() * type_dice) + 1;
    }
    result += parseInt(add_int);
    $("#result_dice").html(result);
}