<?php
 	require_once( "../../lib/configuration.php"	);

class TableRows extends RecursiveIteratorIterator {
	private $id;

    function __construct($it) {
        parent::__construct($it, self::LEAVES_ONLY);
        $this->id = 1;
    }

    function current() {
        return "<td style='width:150px;border:1px solid black;'>" . parent::current(). "</td>";
    }

    function beginChildren() {
        echo "<tr>";
    }
//&quot;test&quot;
	function endChildren() {
		echo '<td style="width:15px;border:1px solid black;">';
		echo '<div class="icon-container">';
		echo '<div id="tab-x" class="button-wrap">';
	    	echo '<span><but  data-id="'.$this->id.'" class="ti-pencil" type="submit"></button></span><span class="icon-name"></span>';
		echo '</div>';
		echo '</div>';
// 		echo '	<span class="ti-pencil" type="submit" id="edituser" value="'.$this->id.'" title="Edit User"></span><span class="icon-name"></span>';
		echo '</td>';
		echo '<td style="width:15px;border:1px solid black;">';
		echo '<div class="icon-container">';
		echo '	<span class="ti-trash" type="submit" value="" title="Delete User"></span><span class="icon-name"></span>';
		echo '</td>';
		echo '</div>';
//
		$this->id++;
        echo "</tr>" . "\n";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Ntobua - Admin Module</title>
  <link rel="stylesheet" href="../../css/themes/jquery-ui-1.11.2.custom/jquery-ui.css">
<!--
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
 -->
  <script src="../../lib/jquery-ui-1.11.0/external/jquery/jquery.js"></script>
  <script src="../../lib/jquery-ui-1.11.0/jquery-ui.js"></script>
  <script src="../../lib/OpenLayers/lib/OpenLayers.js"></script>
  <link rel="stylesheet" href="../../css/themify-icons.css" type="text/css">

<!--   <link rel="stylesheet" href="/resources/demos/style.css"> -->
  <style>
    body { font-size: 62.5%; }
    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    .buttons3 .ui-button { width: 33.33%; }

    tr:nth-of-type(even) {
      background-color:#ccc;
    }

.myclass2 {
    background-color: yellow;
    float: left;
    margin-top: 6px;

}
  </style>
  <script>

$(document).ready(function(){

    var usrdialog, form,

      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
		username = $("#ename"),
		pass = $("#epass"),
		title = $("#etitle"),
		fullname = $("#efullname"),
		position = $("#eposition"),
		email = $("#eemail"),
		phone = $("#ephone"),
		baselanguage = $("#ebaselanguage"),
		activestatus = $("#eactivestatus"),
		loged = $("#eloged"),
		districtid = $("dropdistrict"),
		regionid = $("#dropregion"),
		roleid = $("#droprole"),

      name = $( "#name" ),
//       email = $( "#email" ),
       password = $( "#password" ),
      variable = $( "#evariable" ),
      value = $( "#evalue" ),
      allFields = $( [] ).add( name ).add( email ).add( password ),
      sysconFields = $( [] ).add( variable ).add( value ),
      tips = $( ".validateTips" );

// 	$("#district").keyup(function(){
// 		$.get("../getdistrict.php", {district: $(this).val()}, function(data){
// 			$("datalist").empty();
// 			$("datalist").html(data);
// 		});
// 	});

	$("select#dropregion").change(function(){
	var region_id = $("select#dropregion option:selected").attr('value');
//	 alert(region_id);
	 $("#district").html( "" );
	 if (typeof region_id != 'undefined'){
	 if (region_id.length > 0 ) {

	 $.ajax({
	 type: "POST",
	 url: "../getdistrict2.php",
	 data: "region_id="+region_id,
	 cache: false,
	 beforeSend: function () {
	 $('#district').html('<img src="../../img/loading.gif" alt="" width="24" height="24">');
	 },
	 success: function(html) {
	 $("#district").html( html );
//alert(html);
		$("#dropdistrict :selected").text(feed[0]['districtname']) //the text content of the selected option
		$("#dropdistrict").val(feed[0]['districtid']);
		districtid = $("#dropdistrict"); //feed[0]['districtid'];
	 }
	 });
	 }
	 }
	});
// });
//   $(function() {

 $( ".selector" ).tabs({ selected: 0 });
 $('#tabs').tabs({
	 activate: function (event, ui) {
	 var $activeTab = $('#tabs').tabs('option', 'active');
	//  alert($activeTab);
	 if ($activeTab == 0) {
	 // HERE YOU CAN ADD CODE TO RUN WHEN THE SECOND TAB HAS BEEN CLICKED
		document.getElementById("whichTab").value=0;
	 }
	 if ($activeTab == 1) {
	 // HERE YOU CAN ADD CODE TO RUN WHEN THE SECOND TAB HAS BEEN CLICKED
		document.getElementById("whichTab").value=1;

	 }
	 }
 });

    $( "#radio" ).buttonset();
    $( "#radioL" ).buttonset();

    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }

    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }

    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }

    function addUser() {
      var valid = true;
      allFields.removeClass( "ui-state-error" );

      valid = valid && checkLength( name, "username", 3, 16 );
      valid = valid && checkLength( email, "email", 6, 80 );
      valid = valid && checkLength( password, "password", 5, 16 );

      valid = valid && checkRegexp( name, /^[a-z]([0-9a-z_\s])+$/i, "Username may consist of a-z, 0-9, underscores, spaces and must begin with a letter." );
      valid = valid && checkRegexp( email, emailRegex, "eg. ui@jquery.com" );
      valid = valid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

      if ( valid ) {
        $( "#users tbody" ).append( "<tr>" +
          "<td>" + name.val() + "</td>" +
          "<td>" + email.val() + "</td>" +
          "<td>" + password.val() + "</td>" +
        "</tr>" );
        usrdialog.dialog( "close" );
      }
      return valid;
    }


    usrdialog = $( "#usredit-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "Create an account": addUser,
        Cancel: function() {
          usrdialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        allFields.removeClass( "ui-state-error" );
      }
    });

    sysedit = $( "#sysedit-form" ).dialog({
      autoOpen: false,
      height: 250,
      width: 500,
      modal: true,
      buttons: {
        "Submit": updateSyscon,
        Cancel: function() {
          sysedit.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        sysconFields.removeClass( "ui-state-error" );
      }
    });

    usredit = $( "#usredit-form" ).dialog({
      autoOpen: false,
      height: 750,
      width: 500,
      modal: true,
      buttons: {
        "Submit": updateUser,
        Cancel: function() {
          usredit.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        allFields.removeClass( "ui-state-error" );
      }
    });

    var submitmsg = $( "#submit-message" ).dialog({
      autoOpen: false,
      modal: true,
      tite: "Submit Confirmation",
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
          window.location.reload(true);
        }
      }
    });

    form = usrdialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });


    $( "#create-user" ).button().on( "click", function() {
	    document.getElementById("createuserYesNo").value=1;
    	usrdialog.dialog( "open" );
    });

    $( "#create-user-top" ).button().on( "click", function() {
	    document.getElementById("createuserYesNo").value=1;
    	usrdialog.dialog( "open" );
    });

//     $('.button-wrap but').addClass('icon-container');
    $('.button-wrap but').click( function(event){
        var b = $(this);

      if (document.getElementById("whichTab").value==0){
	       var dbaction = 'getSysteminfo';
	       document.getElementById("whichIdsyscon").value=b;
       }
       else if (document.getElementById("whichTab").value==1){
    	   var dbaction = 'getUserinfo';
	       document.getElementById("whichIduser").value=b;
       }


        var request = OpenLayers.Request.POST({
			url: "jqAdminDBaction.php",
			data: OpenLayers.Util.getParameterString(
			{dbaction: dbaction,
			 id: b.attr('data-id')}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: Userhandler
		});
//        alert(b.attr('data-id'), b.attr('data-middle'), b.attr('data-last'));
    });

 function Userhandler(request){
 if(!request.responseXML) {
		// get the response from php and read the json encoded data
	   feed=JSON.parse(request.responseText);
//  	   alert(feed[0]['returnval']);
//  	   alert(request.responseText);

      if (document.getElementById("whichTab").value==0){
		$("#evariable").val(feed[0]['variable']);
		$("#evalue").val(feed[0]['value']);
		$("#whichIdsyscon").val(feed[0]['id']);

        sysedit.dialog( "open" );

       }
       else if (document.getElementById("whichTab").value==1){
		$("#ename").val(feed[0]['username']);
		$("#epass").val(feed[0]['pass']);
		$("#etitle").val(feed[0]['title']);
		$("#efullname").val(feed[0]['fullname']);
		$("#eposition").val(feed[0]['position']);
		$("#eemail").val(feed[0]['email']);
		$("#ephone").val(feed[0]['phone']);
		$("#ebaselanguage").val(feed[0]['baselanguage']);
		$("#eactivestatus").val(feed[0]['activestatus']);
		$("#eloged").val(feed[0]['loged']);
		$("#dropregion :selected").text(feed[0]['region_name']) //the text content of the selected option
		$("#dropregion").val(feed[0]['regionid']);
		$("#dropregion").change();
// 		$("#dropdistrict :selected").text(feed[0]['districtname']) //the text content of the selected option
// 		$("#dropdistrict").val(feed[0]['districtid']);
//alert(feed[0]['districtid']);
		$("#droprole").val(feed[0]['roleid']);
		$("#whichIduser").val(feed[0]['id']);

        usredit.dialog( "open" );
       }
	}
//  	   document.getElementById("ename").value=feed[0]['returnval'];
} //end function Userhandler(request){

    function updateSyscon(){
		var request = OpenLayers.Request.POST({
			url: "jqAdminDBaction.php",
			data: OpenLayers.Util.getParameterString(
			{dbaction: 'updateSyscon',
			 id: document.getElementById("whichIdsyscon").value,
			 variable: variable.val(),
			 value: value.val()}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: updateSysconhandler
		});

    }

    function updateSysconhandler(request){
		 if(!request.responseXML) {
			// get the response from php and read the json encoded data
		   feed=JSON.parse(request.responseText);
	//	   alert('updateSysconhandler');
			sysedit.dialog( "close" );
			submitmsg.dialog( "open" );
       }
//  	   document.getElementById("ename").value=feed[0]['returnval'];
} //end function updateSysconhandler(request){

    function updateUser(){
    if 	(document.getElementById("createuserYesNo").value==1){
		var request = OpenLayers.Request.POST({
			url: "jqAdminDBaction.php",
			data: OpenLayers.Util.getParameterString(
			{dbaction: 'insertUser',
			id: document.getElementById("whichIduser").value,
			username: username.val(),
			pass: pass.val(),
		title: title.val(),
		fullname: fullname.val(),
		position: position.val(),
		email: email.val(),
		phone: phone.val(),
		baselanguage: baselanguage.val(),
		activestatus: activestatus.val(),
		districtid: districtid.val(),
		regionid: regionid.val(),
		roleid: roleid.val()}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: updateUserhandler
		});
    }else{
		var request = OpenLayers.Request.POST({
			url: "jqAdminDBaction.php",
			data: OpenLayers.Util.getParameterString(
			{dbaction: 'updateUser',
			id: document.getElementById("whichIduser").value,
			username: username.val(),
			pass: pass.val(),
		title: title.val(),
		fullname: fullname.val(),
		position: position.val(),
		email: email.val(),
		phone: phone.val(),
		baselanguage: baselanguage.val(),
		activestatus: activestatus.val(),
		districtid: districtid.val(),
		regionid: regionid.val(),
		roleid: roleid.val()}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: updateUserhandler
		});
	}

    }

    function updateUserhandler(request){
		 if(!request.responseXML) {
			// get the response from php and read the json encoded data
		   feed=JSON.parse(request.responseText);
			usredit.dialog( "close" );
			if (feed[0]['rowsaffected']==1){
			submitmsg.dialog( "open" );
			document.getElementById("createuserYesNo").value=0;
			}
       }
//  	   document.getElementById("ename").value=feed[0]['returnval'];
} //end function updateSysconhandler(request){


 }); //end $(function)
  </script>
</head>
<body>

	<input name="whichTab" type="hidden" id="whichTab" value = 0">
	<input name="createuser" type="hidden" id="createuserYesNo" value = 0">

<div id="tabs">
  <ul>
    <li><a href="#tabs-1">System config</a></li>
    <li><a href="#tabs-2">User admin</a></li>
<!--
    <li><a href="#tabs-3">Aenean lacinia</a></li>
 -->
  </ul>
  <div id="tabs-2">

	<script>
		document.getElementById("whichTab").value=1;
	</script>

	 <div id="submit-message" title="Update complete">
	  <p>
		<span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
				The database update was successfully performed.
	  </p>
	  </div>

	<div id="sysedit-form" title="Maintain System Configuration">
	  <p class="validateTips">All form fields are required.</p>

	  <form>
		<fieldset>
		  <label for="variable">Variable</label>
		  <input type="text" name="variable" id="evariable" value="" class="text ui-widget-content ui-corner-all" readonly>
		  <label for="value">Value</label>
		  <input type="text" name="value" id="evalue" value="" class="text ui-widget-content ui-corner-all" autofocus>
		  <input name="whichIdsyscon" type="hidden" id="whichIdsyscon" value = 0">

<!-- 		  <input type="text" name="loged" id="eloged" value="" class="text ui-widget-content ui-corner-all"> -->
		  <!-- Allow form submission with keyboard without duplicating the dialog button -->
		  <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
		</fieldset>
	  </form>
	</div>

	<div id="usredit-form" title="Edit user accounts usredit-form">
	  <p class="validateTips">All form fields are required.</p>

	  <form>
		<fieldset>
		  <label for="name">Username</label>
		  <input type="text" name="name" id="ename" value="" class="text ui-widget-content ui-corner-all">
		  <label for="pass">Password</label>
		  <input type="text" name="pass" id="epass" value="" class="text ui-widget-content ui-corner-all">
		  <label for="title">Title</label>
		  <input type="text" name="title" id="etitle" value="" class="text ui-widget-content ui-corner-all">
		  <label for="fullname">Full Name</label>
		  <input type="text" name="fullname" id="efullname" value="" class="text ui-widget-content ui-corner-all">
		  <label for="position">Position</label>
		  <input type="text" name="position" id="eposition" value="" class="text ui-widget-content ui-corner-all">
		  <label for="email">Email</label>
		  <input type="text" name="email" id="eemail" value="" class="text ui-widget-content ui-corner-all">
		  <label for="phone">Phone</label>
		  <input type="text" name="phone" id="ephone" value="" class="text ui-widget-content ui-corner-all">
			</p>
			<div id="dropdowns">
			 <div id="center" class="cascade">
			 <?php
			 require_once('../../lib/configuration.php');
			 $sql = "SELECT * FROM area_region ORDER BY regionid";
			 $query = mysqli_query($consqli, $sql);
			 ?>
			 <label>Region:</td>
			 <td><select name="region" id = "dropregion">
			 <option value="">Please Select</option>
			 <?php while ($rs = mysqli_fetch_array($query, MYSQLI_ASSOC )) { ?>
			 <option value="<?php echo $rs["regionid"]; ?>"><?php echo $rs["region_name"]; ?></option>
			 <?php } ?>
			 </select>
			 </label>
			 </div>
			 <div class="cascade" id="district"></div>
			 </div>

			 <?php
			 require_once('../../lib/configuration.php');
			 $sql = "SELECT * FROM usr_roles ORDER BY description";
			 $query = mysqli_query($consqli, $sql);
			 ?>
			 <label>Role:</td>
			 <td><select name="role" id = "droprole">
			 <option value="">Please Select</option>
			 <?php while ($rs = mysqli_fetch_array($query, MYSQLI_ASSOC )) { ?>
			 <option value="<?php echo $rs["id"]; ?>"><?php echo $rs["description"]; ?></option>
			 <?php } ?>
			 </select>
			 </label>

			<p style="text-align: justify;">
		  <label for="baselanguage">Baselanguage</label>
		  <input type="text" name="baselanguage" id="ebaselanguage" value="" class="text ui-widget-content ui-corner-all">
		  <input name="whichIduser" type="hidden" id="whichIduser" value = 0">


		  <div id='radio' class='buttons3'>
		  <label for="radio">Active:
			<input type='radio' id='radio1' name='radio' value='1'><label for="radio1">Yes</label></input>
			<input type='radio' id='radio2' name='radio' value='0'><label for="radio2">No</label></input>
		  </label></div>

<!--
		  <div id='radioL' class='buttons3'>
		  <label for="radioL">Loged:
			<input type='radio' id='radioL1' name='radio' value='1'><label for="radioL1">Yes</label></input>
			<input type='radio' id='radioL2' name='radio' value='2'><label for="radioL2">No</label></input>
		  </div></label>
 -->
<!-- 		  <input type="text" name="loged" id="eloged" value="" class="text ui-widget-content ui-corner-all"> -->
		  <!-- Allow form submission with keyboard without duplicating the dialog button -->
		  <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
		</fieldset>
	  </form>
	</div>

	<div id="users-contain" class="ui-widget">
  <table>
  <tr>
  <td><h1>Existing Users:</h1></td>
  <td><button id="create-user-top">Create new user</button></td>
  </tr>
  </table>
<!-- <h1>Existing Users:</h1> -->
  <table id="users" class="ui-widget ui-widget-content">
    <thead>
      <tr class="ui-widget-header ">

      <?php
      //fill the user admin tab table
       	$statement = $pdo->query('(SELECT t1.`id`, t4.`regionid`, t4.`region_name`, t3.`districtid`, t3.`district_name`,  t1.`username`, t1.`name`, t1.`title`, t1.`position`, t1.`email`,
       				t1.`phone`, t1.`baselanguage`, t1.`activestatus`, t1.`pass`, t1.`loged`, t5.`roleid`
       				FROM `usr_users` t1
       				inner join `usr_user_district` t2 on t1.`username`=t2.`username`
       					inner join `area_district` t3 on t2.`districtid`=t3.`districtid`
       					inner join `area_region` t4 on t3.`regionid`=t4.`regionid`
       					inner join `usr_user_role` t5 on t1.`username`=t5.`username`)
       				UNION
					(SELECT t5.`id`, t6.`regionid`, "n.a", t6.`districtid`, "n.a",  t5.`username`, t5.`name`, t5.`title`, t5.`position`, t5.`email`,
       				t5.`phone`, t5.`baselanguage`, t5.`activestatus`, t5.`pass`, t5.`loged`, t7.`roleid`
       				FROM `usr_users` t5
       				inner join `usr_user_district` t6 on t5.`username`=t6.`username`
       				inner join `usr_user_role` t7 on t5.`username`=t7.`username`
       				WHERE t7.`roleid`=200)
       				order by `regionid`, `districtid`, `username`;');

		$rs1 = $pdo->query('SELECT FOUND_ROWS()');
		$rowCount = (int) $rs1->fetchColumn();

		// write header
		for ($i = 0; $i < $statement->columnCount(); $i++) {
		$col = $statement->getColumnMeta($i);
		echo '<th>'.$col['name'].'</th>';
		}
		?>
      </tr>
    </thead>
    <tbody>
      <?php
      try {
		// write the content from the db table
			$i = 1;
			$result = $statement->setFetchMode(PDO::FETCH_ASSOC);
			foreach(new TableRows(new RecursiveArrayIterator($statement->fetchAll())) as $k=>$v) {
				echo $v;
				if ($i==1) {$uservalue=$v; echo $userval;}
				$i++;
			}
    }
    catch(PDOException $e) {
    	echo "Error: " . $e->getMessage();
	}
    ?>
    </tbody>
  </table>
	</div>
	<button id="create-user">Create new user</button>

  	</div>
  <div id="tabs-1">
  	<script>
		document.getElementById("whichTab").value=0;
	</script>

  <table id="system" class="ui-widget ui-widget-content">
    <thead>
    <tr class="ui-widget-header ">

	  <?php
	  //fill table for tab sys config
		$statement2 = $pdo->query('SELECT * FROM `system_config`');

		$rs2 = $pdo->query('SELECT FOUND_ROWS()');
		$rowCount = (int) $rs2->fetchColumn();

		// write header
		for ($i = 0; $i < $statement2->columnCount(); $i++) {
		$col = $statement2->getColumnMeta($i);
		echo '<th>'.$col['name'].'</th>';
		}
	  ?>
    </tr>
    </thead>
    <tbody>
      <?php
      try {
		// write the content
			$i = 1;
			$result2 = $statement2->setFetchMode(PDO::FETCH_ASSOC);
			foreach(new TableRows(new RecursiveArrayIterator($statement2->fetchAll())) as $k=>$v) {
				echo $v;
				if ($i==1) {$uservalue=$v; echo $userval;}
				$i++;
			}
    }
    catch(PDOException $e) {
    	echo "Error: " . $e->getMessage();
	}
    ?>

  </div>


<!--
  <div id="tabs-3">
    <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
    <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
  </div>
 -->
<!--
</div>
 -->





</body>
</html>