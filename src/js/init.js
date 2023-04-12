const VERBAL_MODE = true;

const IS_INPUT = 0;
const IS_VALUE = 1;
const IS_CHECKBOX = 2;
const IS_CKEDITOR = 3;
const IS_PATH_FILE = 4;
const CKEDITOR5 = Array;

const DISPLAY_CARD = 0;
const DISPLAY_RESUME = 1;
const DISPLAY_EDITABLE = 2;
const DISPLAY_FULL = 3;
const FORMAT_BRUT = 0;
const FORMAT_VIEW = 1;
const FORMAT_EDITABLE = 2;
const FORMAT_ICON = 3;
const FORMAT_BADGE = 4;
const FORMAT_OBJECT = 5;
const FORMAT_ARRAY = 6;
const FORMAT_LIST = 7;
const FORMAT_TEXT = 8;
const FORMAT_PATH = 9;
const FORMAT_LINK = 10;

jQuery(document).ready(function($) {

	"use strict";

	[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {
		new SelectFx(el);
	} );

	$('[data-bd-toggle="tooltip"]').tooltip();
	
});

function init() {

	// Fonctionne pas - sencé recharge la page quand on revient en arrière dans l'historique du navigateur
	window.addEventListener("popstate", function(event) {
		location.reload();
	});

	$('.selectpicker').selectpicker;
	
	$('.draggable').draggable();

	$('.search-trigger').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('.search-trigger').parent('.header-left').addClass('open');
	});

	$('.search-close').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('.search-trigger').parent('.header-left').removeClass('open');
	});

	var tables = document.querySelectorAll("#table");
	tables.forEach(function(userItem) {
		$(userItem).bootstrapTable('destroy').bootstrapTable({
			exportTypes: ['pdf','excel','xlsx','doc','png','csv','xml','json','sql','txt']});
	});

	$('[data-toggle="tooltip"]').tooltip();
}

// Ce code permet de détecter si des touches sont enfoncés et de jouer des fonctions.
var keys = {};
$(window).on("keyup keydown", function (e) {
	keys[e.keyCode] = e.type === 'keydown';

	// Remplacer les numéros par les touches correspondantes disponible sur ce site : https://www.cambiaresearch.com/articles/15/javascript-char-codes-key-codes
	// Mettre si le raccourcis est déjà prit par le navigateur e.preventDefault();

	// ctrl + b -> ouvre le bookmark
	if(keys[17] && keys[66]) {
		User.getBookmark();
		keys = {};
	}
});

function toogleToolbar($forced_hidden = false){
	if($('.app-toolbar-content').hasClass('hidden') == false || $forced_hidden){
		$(".app-toolbar-content").addClass("hidden");
		$(".app-btn-show-toolbar-footer").show("blind", 300);
		$(".dropdown-item-toogletoolbar-button").text("Afficher la barre d'outils");
	} else {
		$(".app-toolbar-content").removeClass("hidden");
		$(".app-btn-show-toolbar-footer").hide("blind", 300);
		$(".dropdown-item-toogletoolbar-button").text("Masquer la barre d'outils");
	}
}
function toogleFooter($forced_hidden = false){
	if($('footer').hasClass('hidden') || $forced_hidden){
		$("footer").addClass("hidden");
		$("footer").hide("blind", 300);
	} else {
		$("footer").removeClass("hidden");
		$("footer").show("blind", 300);
	}
}
function toogleMenu($forced_closed = false){
	if($('.app').hasClass('app-extend') || $forced_closed){
		$(".app").removeClass("app-extend").addClass("app-fold");
		$(".app-nav").hide("drop", 300);
	} else {
		$(".app").removeClass("app-fold").addClass("app-extend");
		$(".app-nav").show("drop", 300);
	}
}
$(document).ready(function() {
    $(window).resize(function() {
        if ($(window).width() < 992) {
            $(".app").addClass("app-compacted");
        } else {
            $(".app").removeClass("app-compacted");
        }
    });
});
