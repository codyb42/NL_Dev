
jQuery(document).ready(function($) {

    tinymce.PluginManager.add('keyup_event', function(editor, url) {
        editor.on('keyup', function(e) {
            //get_ed_content = tinymce.activeEditor.getContent();
	    textareaone= tinyMCE.get('textareaone').getContent();
            textareatwo= tinyMCE.get('textareatwo').getContent();
            textareathree= tinyMCE.get('textareathree').getContent();
            textareafour= tinyMCE.get('textareafour').getContent();
            textareafive= tinyMCE.get('textareafive').getContent();
            textareasix= tinyMCE.get('textareasix').getContent();
	    var textarea = [textareaone, textareatwo, textareathree, textareafour, textareafive, textareasix];

            do_stuff_here(textarea);
	    hide_class();
        });
    });

    // This is needed for running the keyup event in the text (HTML) view of the editor
    $('#content').on('keyup', function(e) {
        get_ed_content = tinymce.activeEditor.getContent();
	textareaone= tinyMCE.get('textareaone').getContent();
        textareatwo= tinyMCE.get('textareatwo').getContent();
        textareathree= tinyMCE.get('textareathree').getContent();
        textareafour= tinyMCE.get('textareafour').getContent();
        textareafive= tinyMCE.get('textareafive').getContent();
        textareasix= tinyMCE.get('textareasix').getContent();

        do_stuff_here(get_ed_content);
    });

    // This function allows the script to run from both locations (visual and text)
    function do_stuff_here(textarea) {
	const images = new Set();
	var output;
	var src = "";
	var tmp = document.createElement('div');
	for(const text of textarea){
		tmp.innerHTML = text;
		var imgs = tmp.getElementsByTagName('img');
		for( const img of imgs) images.add(img.src);
	}
	document.getElementById("nlChooseThumbnail").innerHTML = "";
	for( const img of images) {
		output = document.createElement("IMG");
		output.classList.add("nl_thumbnail_choice");
		output.setAttribute("src", img);
		document.getElementById("nlChooseThumbnail").appendChild(output);
	}

   }
   function hide_class(){
   	var img= document.getElementById("mceu_39-button");
	if(img != null) img.remove();
   }

});


