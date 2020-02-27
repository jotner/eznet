function SetCookie(c_name, value, days) {
  let exdate = new Date()
  exdate.setDate(exdate.getDate() + days)
  document.cookie = c_name + "=" + escape(value) +
    ((days == null) ? "" : ";expires=" + exdate.toGMTString())
  document.getElementById("cookie").style.visibility = "hidden"
}

document.cookie.indexOf("c_name") < 0 && (document.getElementById("cookie").style.visibility = "visible");