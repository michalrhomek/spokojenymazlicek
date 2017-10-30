/* js  faktura  */

function selectLang(url)
{
	$.fancybox.open({
		href: url,
		type: "ajax",
		maxWidth	: 800,
		maxHeight	: 600,
		autoWidth	: true,
		autoHeight	: true,
		fitToView	: false,
		scrolling	: 'no',
		ajax: {
			type: "POST",
			headers: { "cache-control": "no-cache" },
			dataType: "json",
			cache: false,
			data: {
				submitLangAjax : 1
			}
		}
	});
}


/* ------------------------------------------------------------- */
/*  FUNKCE PRO DOBROPIS
/* ------------------------------------------------------------- */
$(function() {				
    $("#submit_slip").click(function() {
		$("#slip_form").submit(function(e) {
       		return false;
		});
					
		$.ajax({
			url: "../modules/add_faktura/ajax.php",
			type:"POST",
			dataType: "json",
			data : {
					submit_slip		: 1,
					slipDate_due	: $(".slipDate_due").val(),
					slipBankNumber	: $(".slipBankNumber").val(),
					slipText		: $(".slipText").val(),
					id_order		: id_order
			},
			success: function(data){
				if(data.ok == 1)
				{
					if (data.slipDate_due)
						$(".slipDate_due").val(data.slipDate_due);
					if (data.slipBankNumber)
						$(".slipBankNumber").val(data.slipBankNumber);
					if (data.slipText)
						$(".slipText").val(data.slipText);
					$(".conf_slip").fadeIn(200).show();
  					$('.conf_slip').delay(3000).slideUp();
				}
				if(data.ok == 2)
				{
					$(".error_slip").fadeIn(200).show();
  					$('.error_slip').delay(3000).slideUp();
				}
			}
		});
    });
});


/* ------------------------------------------------------------- */
/*  FUNKCE PRO ZMENY DATUMU
/* ------------------------------------------------------------- */
	$(function() {				
    	$("#submit_date").click(function() {
			$("#date_updates_form").submit(function(e) {
       			return false;
			});
					
		$.ajax({
			url: "../modules/add_faktura/ajax.php",
			type:"POST",
			dataType: "json",
			data : {
					submit_date		: 1,
					datum			: $(".add").val(),
					datum_inv		: $(".inv").val(),
					datum_dlv		: $(".dlv").val(),
					id_order		: id_order
			},
			success: function(data){
				if(data.ok == 1)
				{
					if (data.add)
						$(".add").val(data.add);
					if (data.inv)
						$(".inv").val(data.inv);
					if (data.dlv)
						$(".dlv").val(data.dlv);
					$(".conf_mk").fadeIn(200).show();
  					$('.conf_mk').delay(3000).slideUp();
				}
				if(data.ok == 2)
				{
					$(".warn_mk").fadeIn(200).show();
  					$('.warn_mk').delay(3000).slideUp();
				}
				if(data.ok == 3)
				{
					$(".error_mk").fadeIn(200).show();
  					$('.error_mk').delay(3000).slideUp();
				}
			}
			});
    	});
	});

	$(function() {				
    	$("#submit_date_due").click(function() {
			$("#date_updates_form").submit(function(e) {
       			return false;
			});
					
		$.ajax({
			url: "../modules/add_faktura/ajax.php",
			type:"POST",
			dataType: "json",
			data : {
					submit_date_due	: 1,
					date_due		: $(".date_due").val(),
					id_order		: id_order
			},
			success: function(data){
				if(data.ok == 1)
				{
					if (data.add)
						$(".date_due").val(data.add);
					$(".conf_due").fadeIn(200).show();
  					$('.conf_due').delay(3000).slideUp();
				}
			}
			});
    	});
	});