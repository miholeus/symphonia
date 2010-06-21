function confirmLink(s)
{
    var is_confirmed = confirm("Вы действительно хотите удалить:\n"+s+"?");
    return is_confirmed;
}

	function all_hide(){
		for(var i=0; i<a.length; i++){
			document.getElementById(a[i]).style.display = "none";
			document.getElementById(a[i]).style.visiblity = "hidden";
		}
	}
	function f_tree(what){
		all_hide();
		document.getElementById(what).style.display = "block";
		document.getElementById(what).style.visiblity = "visible";

	}

function openWin2(t,x,y) {
  myWin= open(t, "", 
    "width="+x+",height="+y+",status=no,toolbar=no,menubar=no");
}
