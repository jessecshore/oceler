$(document).ready(function() {


	$(document).on('click', '#msg_send' , function(event) {

		sendMessage();
		event.preventDefault();
	});

	$(document).on('click', '#msg_cancel' , function(event) {

		clearMessageForm();
		event.preventDefault();
	});

	$(document).on('click', '#share_check_all' , function(event) {
		var share_names = $('input.share-name');
		share_names.prop('checked', true);
		event.preventDefault();
	});

});

/**
 * Serializes the message form data and sends it via
 * POST to the MesageController. On success, calls the
 * clearMessageForm function to reset the form.
 *
 * @return none
 */
function sendMessage()
{

	// Serialize the form data
	msgData = $("#msg_form").serialize();


	// IS THE BELOW STILL NEEDED SINCE THE ENTIRE form
	// IS BEING SERIALIZED?

	// Laravel requires a CSRF token for POST, so we add it
	$.ajaxPrefilter(function(options, originalOptions, xhr) {
    var token = $('#_token').val();

    if(token){
      return xhr.setRequestHeader('X-CSRF-TOKEN', token);
    }
	});

	// Send the message to the Message Controller
	$.post(
		'/message',
		msgData,
		function (data) {
    	clearMessageForm(); // On success, reset the form
		})
		.fail(function () {
  		// Add fail function here
		}
	);
}

/**
 * Resets the message form.
 *
 * @return none
 */
function clearMessageForm()
{
	document.getElementById("msg_form").reset();
	$("#factoid_id").val('');
	$("#share_id").val('');
	$("#share_box").hide();
}

function shareMessage(id)
{
	$("#msg_form #share_id").val(id);
	var orig_msg = $("#msg_" + id + " .msg-body").html();
	$("#share_box").html(orig_msg);
	$("#share_box").show();
}


function createHeader(from, to)
{

	var header;

	// If sender == user header starts with 'You (player_name)' and includes
	// the recipients names
	if(from.user_id == user_id){

		header = 'You (' + user_name + ') to ';

		$.each(to, function( index, value ) {
			header += value.player_name;
			header += (index != to.length -1) ? ', ' : ':'; // Add colon to last
		});
	}

	else{
		header = from.player_name + ':';
	}

	return header;
}

var Message = function(to, sender, msg, factoid, share_id, id)
{
	this.to = to;
	this.user_id = sender.id;
	this.sender_name = sender.player_name;
	this.msg = msg;
	this.factoid = factoid;
	this.share_id = share_id;
	this.id = id;

}

Message.prototype.header = function(){

 	var header;

	// If sender == user header starts with 'You (player_name)' and includes
	// the recipients names
	if(this.user_id == user_id){
		var len = this.to.length;
		header = 'You (' + user_name + ') to ';

		$.each(this.to, function( index, value ) {
			header += value.player_name;
			header += (index != len -1) ? ', ' : ':'; // Add colon to last
		});
	}

	else{
		header = this.sender_name + ':';
	}

	return header;
}

Message.prototype.toHTML = function(){

	// Create div to hold the message
	var div = $('<div>', {id: 'msg_' + this.id, class: 'message'});

	// Add the header
	// (If the sender == user, we add a class to highlight the header)
	header_class = (this.user_id == user_id) ? 'header text-danger' : 'header';

	var header_container = $('<span>', {class: header_class});
	$(header_container).append(this.header());

	// Create span to hold message body
	var msg_body = $('<span>', {class: 'msg-body'});
	$(msg_body).append(this.msg);

	// If a factoid is being shared, add it
	if(this.factoid) {
		var factoid = $('<span>', {class: 'msg-factoid bg-info'});
		$(factoid).append(this.factoid.factoid);
	}

	// Add the reply and share links
	var reply_link = $('<a>', {id: this.id});
	$(reply_link).html('reply');
	$(reply_link).click(function(){
											$("#msg_" + this.id + " .reply-form").show(400);
											$("#msg_" + this.id + " .reply-form input[name='reply']").focus();
										});

	var share_link = $('<a>', {id: this.id});
	$(share_link).html('share');
	$(share_link).click(function(){ shareMessage(this.id) });

	var links = $('<span>');

	$(links).append('(');
	$(links).append($(reply_link));
	$(links).append('/');
	$(links).append($(share_link));
	$(links).append(')');

	$(msg_body).append(links);

	// Add the reply form
	var reply_form = $('<form>', {class: 'reply-form'});
	var reply_prompt = $('<h3>Reply to ' + this.sender_name + ':</h3>');
	var reply = $('<input>', {type: 'text', name: 'message'});
	var parent_msg = $('<input>', {type: 'hidden', name: 'message_id', value: this.id});
	var token = $('<input type="hidden" name="_token" id="token" value="' + $("#_token").val() + '">');
	var reply_send = $('<button type="button" class="btn btn-primary btn-sm pull-right">SEND</button>');
	$(reply_send).click(function(){
												msgData = $(this).parent().serialize();

												$.ajaxPrefilter(function(options, originalOptions, xhr) {
													var token = $('#_token').val();

													if(token){
														return xhr.setRequestHeader('X-CSRF-TOKEN', token);
													}
												});

												// Send the message to the Reply Controller
												$.post(
													'/reply',
													msgData,
													function (data) {
														//$(this).parent().hide(400);
														$(".reply-form").hide(400);
														$("input[name='message']").val('');

													})
													.fail(function () {
														// Add fail function here
													}
												);
											});

	var reply_cancel = $('<button type="button" class="btn btn-primary btn-sm pull-left">CANCEL</button>');
	$(reply_cancel).click(function(){
													$("input[name='reply']").val('');
													$(this).parent().hide(400);
												});

	$(reply_form).append(reply_prompt);
	$(reply_form).append(reply);
	$(reply_form).append(parent_msg);
	$(reply_form).append(token);
	$(reply_form).append(reply_cancel);
	$(reply_form).append(reply_send);
	$(div).append(reply_form);

	$(div).append(header_container);
	$(div).append(factoid);
	$(div).append(msg_body);
	return $(div);
}

Message.prototype.addMessage = function(target){

	// If the message is already in the inbox, remove it
	$("#msg_" + this.id).remove();

	$(target).prepend($(this.toHTML()));
}

/**
 * Reply extends Message
*/
function Reply(to, sender, msg, id)
{
	Message.call(this, to, sender, msg, id);
}

// Must create a Reply prototype that inherits from Message prototype
Reply.prototype = Object.create(Message.prototype);

// Set the "constructor" property to refer to Reply
Reply.prototype.constructor = Reply;

Reply.prototype.header = function(){

 	var header;

	// If sender == user header starts with 'You (player_name)' and includes
	// the recipients names
	if(this.user_id == user_id){

		header = 'You (' + user_name + '):';

	}

	else{
		header = this.sender_name + ':';
	}

	return header;
}

Reply.prototype.toHTML = function(){

	var reply_div = $('<div>', {class: 'message reply'});

	// If the sender == user, we add a class to highlight the header
	reply_header_class = (this.user_id == user_id) ? 'header text-danger' : 'header';
	var reply_header = $('<span>', {class: reply_header_class});
	$(reply_header).append(this.header());

	var reply_body = $('<span>', {class: 'msg-body'});
	$(reply_body).append(this.msg);
	$(reply_div).append($(reply_header));
	$(reply_div).append(reply_body);

	return $(reply_div);
}

Reply.prototype.addMessage = function(target){

	$(target).append($(this.toHTML()));
}

/**
 * SystemMessage extends Message
*/
function SystemMessage(factoid, factoid_id)
{
	this.factoid = factoid;
	this.factoid_id = factoid_id;
}

// Must create a SystemMessage prototype that inherits from Message prototype
SystemMessage.prototype = Object.create(Message.prototype);

// Set the "constructor" property to refer to SystemMessage
SystemMessage.prototype.constructor = SystemMessage;

SystemMessage.prototype.toHTML = function(){

	// Create div to hold the message
	var div = $('<div>', {class: 'message'});

	var header_container = $('<span>', {class: 'header'});
	$(header_container).append('System Message');

	// Create span to hold message body
	var msg_body = $('<span>', {class: 'msg-body'});
	$(msg_body).append(this.msg);

	// If a factoid is being shared, add it
	if(this.factoid) {
		var msg_factoid = $('<span>', {class: 'msg-factoid bg-info'});
		$(msg_factoid).append(this.factoid);
	}

	// Add the share link

	var share_link = $('<a>', {id: this.id});
	$(share_link).html('share');
	$(share_link).click(function(){ shareMessage(this.id) });

	var links = $('<span>');

	$(links).append('(');
	$(links).append($(share_link));
	$(links).append(')');

	$(msg_body).append(links);

	$(div).append(header_container);
	$(div).append(msg_factoid);
	$(div).append(msg_body);
	return $(div);
}

SystemMessage.prototype.addMessage = function(target){
	console.log(this);
	$(target).prepend($(this.toHTML()));
}
