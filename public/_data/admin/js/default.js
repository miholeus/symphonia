/* 
 * Default javascript admin panel functions
 * 
 */
$(document).ready(function() {
    /**
     * tab switcher
     * uri: /admin/sysinfo
     */
    $("#submenu li a").click(function(){
        var active_link = $("#submenu li a.active");
        active_link.removeClass("active");
        $("#page-"+active_link.attr("id")).css("display", "none");

        $("#page-"+$(this).attr("id")).css("display", "block");
        $(this).addClass("active");
        
    })
    /**
     * panel slider
     */
    $("div.pane-sliders .panel .title").click(function(){
        if($(this).hasClass("pane-toggler-down")) {// active panel
            $(this).removeClass("pane-toggler-down").addClass("pane-toggler");
            $(this).parent().find("div.pane-slider").animate({
                "height" : "0px"
            }, 500);
        } else {// inactive panel
            // hide active panel
            $("div.pane-sliders .pane-toggler-down")
                .removeClass("pane-toggler-down").addClass("pane-toggler")
                .parent().find("div.pane-slider").animate({
                "height" : "0px"
            }, 500);
            // animate selected
            $(this).removeClass("pane-toggler").addClass("pane-toggler-down");
            $(this).parent().find("div.pane-slider").animate({
                "height" : "300px"
            }, 500);
        }
    })
    /**
     * Page create/edit section
     * dynamic/static node switcher
     * @uses toggleNodeType()
     */
    $("ul.select-nodes input[type=radio]").click(function(){
        toggleNodeType(this);
    })
});
/**
 * dynamic/static node switcher
 */
function toggleNodeType(element)
{
    var name = $(element).attr("name");
    var value = $(element).attr("value");
    var node_name = name.substring(name.indexOf('[') + 1, name.indexOf(']') );
    var node_type = value == 1 ? 'dynamic' : 'static';
    var hide_type = value == 0 ? 'dynamic' : 'static';

    $("#node-" + node_name + "-" + hide_type).css("display", "none");
    $("#node-" + node_name + "-" + node_type).fadeIn("slow");
}

/**
 * used in admin panel
 * to add new page's node
 */
function deleteNode(node_id, sender)
{
    $("#"+node_id+"-element").remove();
    $("#"+node_id+"-label").remove();
    $(sender).remove();
}
function addNode()
{
//    alert(sender.form);
    var name = prompt("Add new node name");
    if($("#"+name).attr("id") != null) {
        alert('Такой узел страницы уже существует!');
        return false;
    }
    if(name != null) {
       var html = "<tr id='node-" + name + "'><td><a href='javascript:void(0)' onclick='showNode(\"" +
           name + "\")'>" +name + "</a></td><td></td><td></td></tr>";
       $("#custom-nodes").append($(html));
    }
}

function showNode(name, page)
{
    $(".width-60").css("display", "none");
    $(".width-40").animate({
        "width" : "100%"
    }, 300);

    $(".nodes-tabs-list").css("display", "none");

    loadNode(name, page, ".nodes-tabs-content");
}

function loadNode(name, page, container)
{
    if($("#" + name).children().size() > 0) {
        $("#" + name).css("display", "block");
    } else {
        $.post("/admin/contentnode/loadnode", {'page':page, 'node':name}, function(response){
            $(container).append($("<div id='" + name + "'>" + response + "</div>")).
                css("display", "block");
        });
    }
}

function saveNode(node)
{
    $(".width-40").animate({
        "width" : "40%"
    }, 300);

    $("#" + node).css("display", "none");

    $(".width-60").css("display", "block");

    $(".nodes-tabs-list").css("display", "block");
}

function copyNodeToAllPages(node, curPageId)
{
    if(!confirm("Are your sure you want to copy node on ALL pages")) {
        return false;
    }
    $("#ajax-info").css("display", "block");
    $("#ajax-info-img").css("display", "block");

    $.post("/admin/contentnode/copynode", {'node':encodeURI(node), 'page' : curPageId},
            function(response) {
                $("#ajax-info-img").css("display", "none");
                if(response.success == true) {
                    $("#ajax-info-msg").css("color", "#0f0").html('Successfull!');
                } else {
                    $("#ajax-info-msg").css("color", "#f00").html('Error!');
                }
                $("#ajax-info-msg").css("display", "block");
                $("#ajax-info-msg").fadeOut(3000);
            }, 'json'
    );
    return false;
}

function deleteNode(node, nodeId)
{
    if(!confirm("Are you sure you want to delete node")) {
        return false;
    }
    $.post("/admin/contentnode/deletenode", {'nodeId':nodeId}, function(response) {
        if(response.success == true) {
            $("#node-" + node).empty();
            alert('Node deleted!');
        } else {
            alert('Sorry, error occured!')
        }
    }, 'json')
    $(".nodes-tabs-content").find("#" + node).empty();
    return false;
}

/**
 * Submit form handler
 */
function submitbutton(submitParams)
{
    if(submitParams.action != "cancel") {
        if(submitParams.tinymcetext != null) {
            if (tinyMCE.get(submitParams.tinymcetext).isHidden()) {
                tinyMCE.get(submitParams.tinymcetext).show()
            }
            tinyMCE.get(submitParams.tinymcetext).save();
        }
    }
    switch(submitParams.action) {
        case 'save':
            document.forms["adminForm"].submit();
        break;
        case 'cancel':
            location.href = submitParams.uri;
        break;
        case 'delete':
            if(confirm("Are you sure you want to delete selected items?")) {
                submitform();
            }
        break;
    }
}

function submitform()
{
    form = document.adminForm;

  	// Submit the form.
	if (typeof form.onsubmit == 'function') {
		form.onsubmit();
	}
	form.submit();

}
/**
 * Increment/decrement boxcheced values
 * which is used as control sum
 * 
 * @param bool isitchecked
 * @return
 */
function isChecked(isitchecked)
{
	if (isitchecked == true) {
		document.adminForm.boxchecked.value++;
	} else {
		document.adminForm.boxchecked.value--;
	}
}
function checkAll()
{
    $("input[name=cid\[\]]").each(function(){
        if($(this).is(":checked")) {
            $(this).removeAttr("checked");
        } else {
            $(this).attr("checked", "checked");
        }
    })
}