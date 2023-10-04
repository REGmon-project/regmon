(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "../jquery.validate"], factory );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: DE (German, Deutsch)
 */
$.extend($.validator.messages, {
	required: "Dieses Feld ist ein Pflichtfeld.",
	maxlength: $.validator.format("Geben Sie bitte maximal {0} Zeichen ein."),
	minlength: $.validator.format("Geben Sie bitte mindestens {0} Zeichen ein."),
	rangelength: $.validator.format("Geben Sie bitte mindestens {0} und maximal {1} Zeichen ein."),
	email: "Geben Sie bitte eine gültige E-Mail Adresse ein.",
	url: "Geben Sie bitte eine gültige URL ein.",
	date: "Bitte geben Sie ein gültiges Datum ein.",
	number: "Bitte gib ein Zahl ein.", //Geben Sie bitte eine Nummer ein.
	digits: "Bitte gib ein ganze Zahl ein.", //Geben Sie bitte nur Ziffern ein.
	equalTo: "Bitte denselben Wert wiederholen.",
	range: $.validator.format("Bitte gib eine Zahl zwischen {0} und {1} ein."), //Geben Sie bitte einen Wert zwischen {0} und {1} ein.
	max: $.validator.format("Bitte gib ein Zahl kleiner als {0} ein."), //Geben Sie bitte einen Wert kleiner oder gleich {0} ein.
	min: $.validator.format("Bitte gib eine Zahl größer als {0} ein."), //Geben Sie bitte einen Wert größer oder gleich {0} ein.
	creditcard: "Geben Sie bitte eine gültige Kreditkarten-Nummer ein."
});

}));