  // Met la première lettre en majuscule
function ucFirst(str) {return str && str[0].toUpperCase() + str.slice(1);}

// Permet de modifier le badge associer à un élément lorsqu'un range est modifié
function changeRangeText(input){
  let value = input.value;
  let span = $(input).parent().find("span");
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
  let i, j, len = array.length, out = [], obj = {};
  for (i = 0; i < len; i++) {
    obj[array[i]] = 0;
  }
  for (j in obj) {
    out.push(j);
  }
  return out;
}

function detailFormatter(index, row){ // Permet d'afficher les détails dans les tableaux
  let html = []
  $.each(row, function (key, value) {
      if(key == "detailView"){
          html.push(value)
      }
  })
  return html.join('')
}

function replaceSpecialChars(str) {
  const map = {
    'Œ': 'OE',
    'œ': 'oe',
    'Æ': 'AE',
    'æ': 'ae',
    'À': 'A', 'Á': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A',
    'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a',
    'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E',
    'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e',
    'Ì': 'I', 'Í': 'I', 'Î': 'I', 'Ï': 'I',
    'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
    'Ò': 'O', 'Ó': 'O', 'Ô': 'O', 'Õ': 'O', 'Ö': 'O', 'Ø': 'O',
    'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ø': 'o',
    'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'U',
    'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u',
    'Ç': 'C', 'ç': 'c',
    'Ñ': 'N', 'ñ': 'n',
    'Ý': 'Y', 'ý': 'y', 'ÿ': 'y'
  };
  return str.replace(/[\u00C0-\u017F]/g, (c) => map[c] || c);
};
