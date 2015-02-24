/**
 * Copyright 2015 Amazon.com, Inc., or its Affiliates.
 */

function amznexpressplugin() {
	return sc_text;
}

(function() {

	tinymce.create('tinymce.plugins.amznexpressplugin', {

		init : function(ed, url){
			ed.addButton('amznexpressplugin', {
				title : 'Insert Express Checkout Shortcode',
			onclick : function() {
				ed.execCommand(
					'mceInsertContent',
					false,
					amznexpressplugin()
					);
			},
			image : sc_img
			});
		},

	getInfo : function() {
		return {
			longname : 'Pay with Amazon Express TinyMCE Shortcode plugin',
	author : 'Pay with Amazon',
	authorurl : 'http://payments.amazon.com',
	infourl : 'http://payments.amazon.com',
	version : "1.0"
		};
	}

	});

	tinymce.PluginManager.add('amznexpressplugin', tinymce.plugins.amznexpressplugin);

})();	
