function change_password()
{
  document.getElementById("change_password_button").style.display="none";
  document.getElementById("changepassform").style.display="block";
  document.getElementById("back_button").style.display="block";
} 
function edit_about()
{ 
      document.getElementById("abouttable").style.display="none";
      document.getElementById("change_password_button").style.display="none";
      document.getElementById("edit_button").style.display="none";
      document.getElementById("abouttableedit1").style.display="block";
      document.getElementById("back_button").style.display="block";
}
function back_about()
{ 
      document.getElementById("abouttable").style.display="block";
      document.getElementById("change_password_button").style.display="";
      document.getElementById("edit_button").style.display="block";
      document.getElementById("abouttableedit1").style.display="none";
      document.getElementById("back_button").style.display="none";
      document.getElementById("change_password_button").style.display="block";
      document.getElementById("changepassform").style.display="none";
}
function edit_photo(){
      document.getElementById("edit_description").style.display="none";
      document.getElementById("change_description").style.display="block";
      document.getElementById("change_button").style.display="block";
}
function edit_photo(x){
      document.getElementById("change_description" + x).className="form-control";
      document.getElementById("change_button" + x).className="btn btn-primary";
      document.getElementById("edit_cancel" + x).className="btn btn-danger";
      document.getElementById("delete_Photo" + x).className="btn btn-danger d-none";
      document.getElementById("edit_description" + x).className="btn btn-primary d-none";
      document.getElementById("description" + x).className="text-center d-none";
}
function cancel_edit_photo(x){
      document.getElementById("confirm" + x).className="d-none";
      document.getElementById("delete_button" + x).className="btn btn-danger d-none";
      document.getElementById("change_description" + x).className="form-control d-none";
      document.getElementById("change_button" + x).className="btn btn-primary d-none";
      document.getElementById("edit_cancel" + x).className="btn btn-danger d-none";
      document.getElementById("delete_Photo" + x).className="btn btn-danger";
      document.getElementById("edit_description" + x).className="btn btn-primary";
      document.getElementById("description" + x).className="text-center";
}
function delete_photo(x){
      document.getElementById("confirm" + x).className="d-block";
      document.getElementById("delete_button" + x).className="btn btn-danger";
      document.getElementById("delete_Photo" + x).className="btn btn-danger d-none";
      document.getElementById("edit_description" + x).className="btn btn-primary d-none";
      document.getElementById("edit_cancel" + x).className="btn btn-danger";
}
function delete_comment(x){
      document.getElementById("confirm_comment" + x).className="d-block";
      document.getElementById("delete_button_comment" + x).className="btn btn-danger";
      document.getElementById("delete_Photo_comment" + x).className="btn btn-danger d-none";
      document.getElementById("edit_description_comment" + x).className="btn btn-primary d-none";
      document.getElementById("edit_cancel_comment" + x).className="btn btn-primary";
}
function cancel_delete_comment(x){
      document.getElementById("confirm_comment" + x).className="d-none";
      document.getElementById("delete_button_comment" + x).className="btn btn-danger d_none";
      document.getElementById("delete_Photo_comment" + x).className="btn btn-danger";
      document.getElementById("edit_description_comment" + x).className="btn btn-primary";
      document.getElementById("edit_cancel_comment" + x).className="btn btn-danger d-none";
}
function remove_friend(){
      document.getElementById("warning_label").className="d-inline";
      document.getElementById("confirm_remove_friend").className="btn btn-danger ml-3 d-inline";
      document.getElementById("remove_friend").className="btn btn-danger ml-3 d-none";
      document.getElementById("cancel_remove_friend").className="btn btn-primary ml-3 d-inline";
}
function cancel_remove_friend(){
      document.getElementById("warning_label").className="d-none";
      document.getElementById("confirm_remove_friend").className="btn btn-danger ml-3 d-none";
      document.getElementById("remove_friend").className="btn btn-danger ml-3 d-inline";
      document.getElementById("cancel_remove_friend").className="btn btn-primary ml-3 d-none";
}