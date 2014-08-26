/**
 * Cristi Citea (http://zend-form-generator.123easywebsites.com/)
 *
 * @link      https://github.com/patrioticcow/Zend-Form for the canonical source repository
 * @copyright Copyright (c) 2012 Cristi Citea
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Form_Generator
 */

$(document).ready(function() {
	 
	var uniqueId = $('#the_form li').length + 1;
	var theForm = $('#the_form');
	var secondTab = $('#add_form_element li:eq(1) a');
	var thirdTab = $('#add_form_element li:eq(2) a');
	var fieldProp = $('#field_properties');

	$(".delete_li").on("click", function () {
		$(this).parent().remove();
	});
	
	$(".edit_form_checkbox .edit_li").on("click", function () {
		var liId = $(this).parent().attr('id'),
				size = $(this).parent().find('.span_checkbox').length();
		secondTab.tab('show');
		$.get(basepath+'/formgen/checkbox', { name: "Edit Checkbox Field", id: liId, 'length': size}).done(function(data) {
			fieldProp.html(data);
              editLineText(liId, 'line_checkbox');
	    });
	});
	
	$(".edit_form_radio .edit_li").on("click", function () {
		var liId = $(this).parent().attr('id'),
				size = $(this).parent().find('.span_radio').size();

		secondTab.tab('show');
		$.get(basepath+'/formgen/radio', { name: "Edit Radio Field", id: liId, 'length': size}).done(function(data) {
			fieldProp.html(data);
	      editLineText(liId, 'line_radio');
	    });
	});
	
	$(".edit_form_text .edit_li").on("click", function () {
		var liId = $(this).parent().attr('id');
		secondTab.tab('show');
		$.get(basepath+'/formgen/input', { name: "Editer le champ texte", id: liId}).done(function(data) {
			fieldProp.html(data);
            editLineText(liId, 'line_text');
	    });
	});
	
	$(".edit_form_paragraph .edit_li").on("click", function () {
		var liId = $(this).parent().attr('id');
		secondTab.tab('show');
		$.get(basepath+'/formgen/paragraph', { name: "Editer le champ paragraphe", id: liId}).done(function(data) {
			fieldProp.html(data);
            editLineText(liId, 'line_paragraph');
	    });
	});
	
	$(".edit_form_upload .edit_li").on("click", function () {
		var liId = $(this).parent().attr('id');
		secondTab.tab('show');
		$.get(basepath+'/formgen/upload', { name: "Editer le champ fichier", id: liId}).done(function(data) {
			fieldProp.html(data);
            editLineText(liId, 'line_upload');
	    });
	});
	
	$(".edit_form_url .edit_li").on("click", function () {
		var liId = $(this).parent().attr('id');
		secondTab.tab('show');
		$.get(basepath+'/formgen/url', { name: "Edit Web Site / Url Field", id: liId}).done(function(data) {
			fieldProp.html(data);
            editLineText(liId, 'line_url');
	    });
	});

	$('#add_form_element a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});

    $('.edit_form_properties').click(function (e) {
		e.preventDefault();
        thirdTab.tab('show');
	});

    /**
     * add_form_properties
     */
    $('#add_form_properties').click(function(e){
        e.preventDefault();
        
        var formTitle = $('.form_title_head');
        formTitle.find('.form_title_placeholder').html(
            $('[name="form_title"]').val()
        );
        formTitle.find('.form_description_placeholder').html(
            $('[name="form_description"]').val()
        );
        formTitle.find('[name="form_model_placeholder"]').attr('value',
            $('[name="form_model"]').val()
        );
        formTitle.find('[name="form_class_placeholder"]').attr('value',
            $('[name="form_class"]').val()
        );
        formTitle.find('[name="form_id_placeholder"]').attr('value',
            $('[name="form_id"]').val()
        );
        formTitle.find('[name="form_namespace_placeholder"]').attr('value',
            $('[name="form_namespace"]').val()
        );
        formTitle.find('[name="form_class_name_placeholder"]').attr('value',
            $('[name="form_class_name"]').val()
        );

        formTitle.css({'background-color' : '#87ffc1'}).animate({backgroundColor: '#ffffff'},{duration:1000});
    });

	/**
	 * single_line_text
	 */
	$('#single_line_text').click(function () {
		var liId = "edit_form_text" + uniqueId;

		line_text(liId, theForm, fieldProp, uniqueId, "edit_form_text");

		uniqueId++;
	});

	/**
	 * line_date
	 */
	$('#line_date').click(function () {
		var liId = "edit_form_date" + uniqueId;

        line_date(liId, theForm, fieldProp, uniqueId, "edit_form_date");

		$('#'+ liId +' .edit_li').click(function () {
			secondTab.tab('show');
			$.get(basepath+'/formgen/date', { name: "Edit Date Field", id: liId}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_date');
		    });
		});

		uniqueId++;
	});

	/**
	 * line_number
	 */
	$('#line_number').click(function () {
		var liId = "edit_form_number" + uniqueId;

		line_number(liId, theForm, fieldProp, uniqueId, "edit_form_number");

		$('#'+ liId +' .edit_li').click(function () {
			secondTab.tab('show');
			$.get(basepath+'/formgen/number', { name: "Edit Number Field", id: liId}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_number');
		    });
		});

		uniqueId++;
	});

	/**
	 * line_phone
	 */
	$('#line_phone').click(function () {
		var liId = "edit_form_number" + uniqueId;

		line_phone(liId, theForm, fieldProp, uniqueId, "edit_form_phone");

		$('#'+ liId +' .edit_li').click(function () {
			secondTab.tab('show');
			$.get(basepath+'/formgen/phone', { name: "Edit Phone Field", id: liId}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_phone');
		    });
		});

		uniqueId++;
	});

	/**
	 * line_password
	 */
	$('#line_password').click(function () {
		var liId = "edit_form_password" + uniqueId;

        line_password(liId, theForm, fieldProp, uniqueId, "edit_form_password");

		$('#'+ liId +' .edit_li').click(function () {
			secondTab.tab('show');
			$.get(basepath+'/formgen/password', { name: "Edit Password Field", id: liId}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_password');
		    });
		});

		uniqueId++;
	});

	/**
	 * line_password_verify
	 */
	$('#line_password_verify').click(function () {
		var liId = "edit_form_password_verify" + uniqueId;

        line_password_verify(liId, theForm, fieldProp, uniqueId, "edit_form_password_verify");

		$('#'+ liId +' .edit_li').click(function () {
			secondTab.tab('show');
			$.get(basepath+'/formgen/passwordverify', { name: "Edit Password Verify Field", id: liId}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_password_verify');
		    });
		});

		uniqueId++;
	});

	/**
	 * line_email
	 */
	$('#line_email').click(function () {
		var liId = "edit_form_email" + uniqueId;

        line_email(liId, theForm, fieldProp, uniqueId, "edit_form_email");

		$('#'+ liId +' .edit_li').click(function () {
			secondTab.tab('show');
			$.get(basepath+'/formgen/email', { name: "Edit Email Field", id: liId}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_email');
		    });
		});

		uniqueId++;
	});

	/**
	 * line_paragraph
	 */
	$('#line_paragraph').click(function () {
		var liId = "edit_form_paragraph" + uniqueId;

		line_paragraph(liId, theForm, fieldProp, uniqueId, "edit_form_paragraph");

		uniqueId++;
	});

	/**
	 * line_checkbox
	 */
	$('#line_checkbox').click(function () {
		var liId = "edit_form_checkbox" + uniqueId;

		line_checkbox(liId, theForm, fieldProp, uniqueId, "edit_form_checkbox");

		$('#'+ liId +' .edit_li').click(function () {
            var lengthNr = $(this).parent('li').find('.checkbox').length;

			secondTab.tab('show');
			$.get(basepath+'/formgen/checkbox', { name: "Edit Checkbox Field", id: liId, 'length': lengthNr}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_checkbox');
		    });
		});

		uniqueId++;
	});

	/**
	 * line_radio
	 */
	$('#line_radio').click(function () {
		var liId = "edit_form_radio" + uniqueId;

		line_radio(liId, theForm, fieldProp, uniqueId, "edit_form_radio");

		$('#'+ liId +' .edit_li').click(function () {
            var lengthNr = $(this).parent('li').find('.radio').length;

			secondTab.tab('show');
			$.get(basepath+'/formgen/radio', { name: "Edit Radio Field", id: liId, 'length': lengthNr}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_radio');
		    });
		});

		uniqueId++;
	});

	/**
	 * line_dropdown
	 */
	$('#line_dropdown').click(function () {
		var liId = "edit_form_dropdown" + uniqueId;

		line_dropdown(liId, theForm, fieldProp, uniqueId, "edit_form_dropdown");

		$('#'+ liId +' .edit_li').click(function () {
            var lengthNr = $(this).parent('li').find('.dropdown').find('option').length;

			secondTab.tab('show');
			$.get(basepath+'/formgen/dropdown', { name: "Edit Drop Down Field", id: liId, 'length': lengthNr}).done(function(data) {
				fieldProp.html(data);
                editLineText(liId, 'line_dropdown');
			});
		});

		uniqueId++;
	});

    /**
     * line_upload
     */
    $('#line_upload').click(function () {
        var liId = "edit_form_text" + uniqueId;

        line_upload(liId, theForm, fieldProp, uniqueId, "edit_form_upload");

        uniqueId++;
    });

    /**
     * line_credit_card
     */
    $('#line_credit_card').click(function () {
        var liId = "edit_form_credit_card" + uniqueId;

        line_credit_card(liId, theForm, fieldProp, uniqueId, "edit_form_credit_card");

        $('#'+ liId +' .edit_li').click(function () {
            secondTab.tab('show');
            $.get(basepath+'/formgen/creditcard', { name: "Edit Credit Card Field", id: liId}).done(function(data) {
                fieldProp.html(data);
                editLineText(liId, 'line_credit_card');
            });
        });

        uniqueId++;
    });

    /**
     * line_url
     */
    $('#line_url').click(function () {
        var liId = "edit_form_url" + uniqueId;

        line_url(liId, theForm, fieldProp, uniqueId, "edit_form_url");

        uniqueId++;
    });

    /**
     * line_hidden
     */
    $('#line_hidden').click(function () {
        var liId = "edit_form_hidden" + uniqueId;

        line_hidden(liId, theForm, fieldProp, uniqueId, "edit_form_hidden");

        $('#'+ liId +' .edit_li').click(function () {
            secondTab.tab('show');
            $.get(basepath+'/formgen/hidden', { name: "Edit Hidden Field", id: liId}).done(function(data) {
                fieldProp.html(data);
                editLineText(liId, 'line_hidden');
            });
        });

        uniqueId++;
    });

	/**
	 * generate the form
	 * convert form to json
	 */
	$(document).on("click", "#generate_form_button", function(e){
		e.preventDefault();

        var allData = [];

		var li = $('#the_form li');

        var addFormProperties = formPropertiesJson($('.form_title_head'));
        allData.push({'form_properties' : addFormProperties});

		li.each(function(index, data) {
		    if($(this).hasClass('edit_form_text') === true) {
		        var addLineText = lineTextJson($(this));
                allData.push({'line_text' : addLineText});
		    }
            if($(this).hasClass('edit_form_date') === true) {
		        var addLineDate = lineDateJson($(this));
                allData.push({'line_date' : addLineDate});
		    }
            if($(this).hasClass('edit_form_paragraph') === true) {
		        var addParagraphText = lineParagraphJson($(this));
                allData.push({'line_paragraph' : addParagraphText});
		    }
            if($(this).hasClass('edit_form_number') === true) {
		        var addNumberText = lineNumberJson($(this));
                allData.push({'line_number' : addNumberText});
		    }
            if($(this).hasClass('edit_form_checkbox') === true) {
		        var addCheckboxText = lineCheckboxJson($(this));
                allData.push({'line_checkbox' : addCheckboxText});
		    }
            if($(this).hasClass('edit_form_radio') === true) {
		        var addRadioText = lineRadioJson($(this));
                allData.push({'line_radio' : addRadioText});
		    }
            if($(this).hasClass('edit_form_dropdown') === true) {
		        var addDropdownText = lineDropdownJson($(this));
                allData.push({'line_dropdown' : addDropdownText});
		    }
            if($(this).hasClass('edit_form_password') === true) {
		        var addPasswordText = linePasswordJson($(this));
                allData.push({'line_password' : addPasswordText});
		    }
            if($(this).hasClass('edit_form_password_verify') === true) {
		        var addPasswordVerifyText = linePasswordVerifyJson($(this));
                allData.push({'line_password_verify' : addPasswordVerifyText});
		    }
            if($(this).hasClass('edit_form_email') === true) {
		        var addEmailText = lineEmailJson($(this));
                allData.push({'line_email' : addEmailText});
		    }
            if($(this).hasClass('edit_form_upload') === true) {
		        var addUploadText = lineUploadJson($(this));
                allData.push({'line_upload' : addUploadText});
		    }
            if($(this).hasClass('edit_form_credit_card') === true) {
		        var addCreditCardJson = lineCreditCardJson($(this));
                allData.push({'line_credit_card' : addCreditCardJson});
		    }
            if($(this).hasClass('edit_form_url') === true) {
		        var addUrlJson = lineUrlJson($(this));
                allData.push({'line_url' : addUrlJson});
		    }
            if($(this).hasClass('edit_form_hidden') === true) {
		        var addHiddenJson = lineHiddenJson($(this));
                allData.push({'line_hidden' : addHiddenJson});
		    }
		});

        var formTitle = addFormProperties[0].title.trim().replace(/ /g,'');

        $('#form_template').val($('#the_form').html());
        $('#form_jsonified').val(JSON.stringify(allData));
        $('form:first').submit()
        return false;
        //setLocalStorage(formTitle, allData);
	});

    var setLocalStorage = function (key, value){
        if(typeof(Storage)!=="undefined"){
            localStorage.setItem(key, JSON.stringify(value));
            if (localStorage.key){
                window.open(
                	basepath+"/formgen/view/" + key,
                    '_blank'
                );
            }
        }
        else
        {
            alert("Sorry, your browser does not support web storage...");
        }
    }

	// maybe i'll do a plugin
	/*
	var defaults = {
			test : 'test'
	};

	var formElements = function(options){
		var config = $.extend({}, defaults, options);
	};
	*/

});