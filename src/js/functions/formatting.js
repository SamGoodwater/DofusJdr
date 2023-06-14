  // Met la première lettre en majuscule
function ucFirst(str) {return str && str[0].toUpperCase() + str.slice(1);}

// Permet de modifier le badge associer à un élément lorsqu'un range est modifié
function changeRangeText(input){
  var value = input.value;
  var span = $(input).parent().find("span");
  switch (value) {
      case '0':
          span.text("Aucun");
          span.attr("title","Aucun");
          span.removeClass("border-blue-d-3").removeClass("border-green-d-1").addClass("border-grey-d-3");
          span.removeClass("text-blue-d-3").removeClass("text-green-d-1").addClass("text-grey-d-3");
      break;
      case '1':
          span.text("Lecture");
          span.attr("title","Lecture");
          span.removeClass("border-grey-d-3").removeClass("border-green-d-3").addClass("border-blue-d-3");
          span.removeClass("text-grey-d-3").removeClass("text-green-d-3").addClass("text-blue-d-3");
      break;
      case '2':
          span.text("Écriture");
          span.attr("title","Écriture");
          span.removeClass("border-blue-d-3").removeClass("border-grey-d-3").addClass("border-green-d-3");
          span.removeClass("text-blue-d-3").removeClass("text-grey-d-3").addClass("text-green-d-3");
      break;
      default:
        console.log("Nda");
  }
}

  
function array_unique(array) { //cleanArray removes all duplicated elements
  var i, j, len = array.length, out = [], obj = {};
  for (i = 0; i < len; i++) {
    obj[array[i]] = 0;
  }
  for (j in obj) {
    out.push(j);
  }
  return out;
}

function detailFormatter(index, row){ // Permet d'afficher les détails dans les tableaux
  var html = []
  $.each(row, function (key, value) {
      if(key == "detailView"){
          html.push(value)
      }
  })
  return html.join('')
}