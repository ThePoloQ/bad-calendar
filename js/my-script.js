$(document).ready(function(){
  $('.jsfileinput').each(function(){
    var $input = $(this);
    var arrExt = $input.attr('jq-exts').split(' ');
    $input.hide();this.value='';
    
    var $button = $('<button type="button" class="btn btn-default">Parcourir</button>');
    var $text = $('<span style="margin-right:10px;">Aucun fichier selectionné</span>');
    var $wdiv = $('<div class="jq-file-widget"></div>');
    
    $button.click(function(){
      $input.click();
    });
    
    $input.change(function(e){
      var fn=this.value
      fExt=fn.split('.');
      
      //on vérifie l'extension du fichier
      if($.inArray(fExt[fExt.length-1],arrExt) == -1){
        this.value='';
        $text.text('Aucun fichier selectionné');
        alert('Mauvais fichier'); 
        return;
      }
      $text.text(this.value)
    });
    
    $wdiv.append($text).append($button);
    
    //on ajoute le tout à la page
    $input.after($wdiv);
  });

});