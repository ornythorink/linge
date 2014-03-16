<script type="text/javascript" src="{$j_basepath}js/jquery-1.7.2.min.js" ></script>

<script>
uri3 = "{jurl 'bo~category:updateCategories'}";  

{literal}
	$(document).ready(function() {
          $(".whiteliste").click(function() {  
              var target = $(this).attr('id');
              var brokenstring = target.split('-');           
               
                $.post(uri3, { id:  brokenstring[1], source:  brokenstring[2], boutique: brokenstring[3] },
                   function(data) {
                      
                   });
               $("#ligne-" + brokenstring[1] ).remove(); 
          });
     });
     
{/literal}       
</script>	

<table class="records-list">
<thead>
<tr>
        <th>&nbsp;</th>
</tr>
</thead>
<tbody>
    
 
{foreach $liste as $record}
<tr  id="ligne-{$record->id_produit}"  class="{cycle array('odd','even')}">    
    <td>
    {$record->boutique}
    </td> 
    <td  style="text-align:right;" nowrap="nowrap" >
		{$record->categorie_marchand}
    </td>
    <td  nowrap="nowrap" >
    &nbsp;&nbsp;&nbsp;<a id="whiteliste-{$record->id_produit}-{$record->source}-{$record->boutique}"   class="whiteliste"  >Whitelister</a>&nbsp;&nbsp;&nbsp;    
    </td>

</tr>
{/foreach}
</tbody>
</table>