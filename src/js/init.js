const VERBAL_MODE = true;

const IS_INPUT = 0;
const IS_VALUE = 1;
const IS_CHECKBOX = 2;
const IS_CKEDITOR = 3;
const IS_PATH_FILE = 4;
const CKEDITOR5 = Array;

const FORMAT_BRUT = 0;
const FORMAT_VIEW = 1;
const FORMAT_MODIFY = 2;
const FORMAT_ICON = 3;
const FORMAT_BADGE = 4;
const FORMAT_OBJECT = 5;
const FORMAT_IMAGE = 6;
const FORMAT_ARRAY = 7;
const FORMAT_FANCY = 8;
const FORMAT_LIST = 9;
const FORMAT_TEXT = 10;
const FORMAT_PATH = 11;
const DISPLAY_CARD = 12;
const FORMAT_LINK = 13;
const DISPLAY_RESUME = 14;

jQuery(document).ready(function($) {

	"use strict";

	[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {
		new SelectFx(el);
	} );

	$('#menuToggle').on('click', function(event) {
		$('body').toggleClass('open');
	});

	$('[data-bd-toggle="tooltip"]').tooltip();
	
});

function init() {
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
