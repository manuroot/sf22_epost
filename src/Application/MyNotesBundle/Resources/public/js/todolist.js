
$(document).ready(function(){

	// Get items
	function getItems(exampleNr)
	{
		var columns = [];

		$(exampleNr + ' ul.sortable-list').each(function(){
			columns.push($(this).sortable('toArray').join(','));				
		});

		return columns.join('|');
	}

	// Load items from cookie
	function loadItemsFromCookie(name)
	{
		if ( $.cookie(name) != null )
		{
			renderItems($.cookie(name));
		}
		else 
		{
			alert('No items saved in "' + name + '".');
		}	
	}

	// Render items
	function renderItems(items)
	{
		var html = '';
                //y()=("1,2,4"|)
		var columns = items.split('|');
		for ( var c in columns )
		{
			html += '<div class="column left';
            		if ( c == 0 )
			{
				html += ' first';
			}
        		html += '"><ul class="sortable-list">';
        		if ( columns[c] != '' )
			{
				var items = columns[c].split(',');
                		for ( var i in items )
				{
					html += '<li class="sortable-item" id="' + items[i] + '">Sortable item ' + items[i] + '</li>';
				}
			}

			html += '</ul></div>';
		}
		$('#example-2-4-renderarea').html(html);
	}

	// Example 2.1: Get items
	/*$('#example-2-1 .sortable-list').sortable({
		connectWith: '#example-2-1 .sortable-list'
	});*/

	$('#btn-get').click(function(){
		alert(getItems('#example-2-1'));
	});
        // Example 2.2: Save items
	$('#btn-save').click(function(){
		$.cookie('cookie-a', getItems('#example-2-1'));

		alert('Items saved (' + $.cookie('cookie-a') + ')');
	});

	// Example 2.3: Save items automaticly
	$('#example-2-1 .sortable-list').sortable({
             placeholder: "ui-sortable-placeholder",
		connectWith: '#example-2-1 .sortable-list',
		update: function(){
			$.cookie('cookie-a', getItems('#example-2-1'));
		}
	});


	// Example 2.4: Load items
	$('#btn-load-example').click(function(){
		renderItems(getItems('#example-2-1'));
	});

	$('#btn-load-cookie-a').click(function(){
		loadItemsFromCookie('cookie-a');	
	});

	$('#btn-load-cookie-b').click(function(){
		loadItemsFromCookie('cookie-b');	
	});

});
 