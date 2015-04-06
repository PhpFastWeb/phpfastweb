function showhide (id, set) {
  divobj = document.getElementById(id);
  if (set == false) {
    divobj.style.display = 'none';
  } else {
    divobj.style.display = 'block';
  }
  return true;
}