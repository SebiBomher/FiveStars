
function edit_about()
{ 
      document.getElementById("abouttable").style.display="none";
      document.getElementById("edit").style.display="none";
      document.getElementById("abouttableedit1").style.display="block";
      document.getElementById("back").style.display="inline"; 
}
function back_about()
{ 
      document.getElementById("abouttable").style.display="block";
      document.getElementById("edit").style.display="block";
      document.getElementById("abouttableedit1").style.display="none";
      document.getElementById("back").style.display="none"; 
}