<?php $this->load->view('header', array('num' => 6, 'title' => "Patients Treated")); ?>
<body>
<div class="element-container">
	<div class="row center-block">
		<div class="col-xs-12 columns">
			<legend>Patients Treated</legend>
			
			<div class="col-xs-12 col-lg-6 col-lg-offset-3 columns">
				<!-- FIRST COLUMN -->
				<div class="col-xs-6 col-lg-6 columns">
					<label for="chartop" style="display:flex">Choose Chart Type</label>
						<select class="form-control" id="chartop">
							<option value="0">Barchart</option>
							<option value="1">Linechart</option>
						</select>
				</div>
				
				<!-- SECOND COLUMN -->
				<div class="col-xs-6 col-lg-6 columns">
					<label for="span">Choose Timespan</label>
						<select id="span" class="form-control" onchange="SelectSpan(this)">
							<option value="non"></option>
							<option value="0">Yearly</option>
							<option value="1">Monthly</option>
						</select>
				</div>

				<!-- THIRD COLUMN -->
				<div class="col-xs-6 col-lg-6 columns">
					<button id="year" class="btn btn-primary form-elem" style="display:none;" onclick="displayYear()">Choose Year</button>
					<div id="ScrollCB1" class="scroll-select form-control form-elem" style="display:none;"></div>
				</div>
				
				<!-- FOURTH COLUMN -->
				<div class="col-xs-6 col-lg-6 columns">
					<button id="months" class="btn btn-primary form-elem" style="display:none;" onclick="displayMonths()">Choose Months</button>
					<div id="ScrollCB" class="scroll-select form-control form-elem months-select" style="display:none;"></div>
				</div>
			</div>
			
			<!-- GENERATE REPORT BUTTON CONTAINER -->
			<div class="col-xs-12 columns">
				<hr />
				<button type="button" class="btn btn-success form-elem" onclick="generateReport()">
					<span class="glyphicon glyphicon-folder-open"></span> 
					Generate Report
				</button>
			</div>
		</div>		
	</div>
</div>

<!--  VIEW OF GENERATED REPORT -->
<div style class="element-container">
	<div class="row center-block">
		<div class="col-xs-12 columns">
			<div style="width:80%">
				<canvas id="myChart" height="500" width="667" style="width:667px; height:500px;"></canvas>
			</div>
		</div>
		<!-- <div id="results" class="col-xs-3 columns" style="display:none">
			<button class="btn btn-primary" id="pdata" style="width:100%;" onclick="showresult()">Report Details</button><br>
			<div id="patientdata" class="scroll-select form-control">
			</div>
		</div> -->
	</div>
</div>

<?php $this->load->view('footer'); ?>
<script type="text/javascript">
function generateReport(){
var spantype=(document.getElementById("span").value);
var data=checkinputs(spantype);
if(data[0]<0 || spantype=="non")
	alert("Please supply all the necessary data to generate the report.");
else{
	spantype=parseInt(spantype);
var points=new Array();
data[1][spantype]=data[1][spantype].split("to");
data[1][spantype][0]=data[1][spantype][0].replace(/\s+/g, '');
data[1][spantype][data[1][spantype].length-1]=data[1][spantype][data[1][spantype].length-1].replace(/\s+/g, '');
if(spantype==0){
	if(data[1][spantype][0]>data[1][spantype][data[1][spantype].length-1]){
	temp=data[1][spantype][0];
		data[1][spantype][0]=data[1][spantype][data[1][spantype].length-1];
		data[1][spantype][data[1][spantype].length-1]=temp;
}

var date=new Date();
var month=date.getMonth()-1;
var mdaynum=new Array (31,28,31,30,31,30,31,31,30,31,30,31);
var day=mdaynum[month];
month+=1;
if(month<10)
month='-0'+month;
if((parseInt(data[1][spantype][data[1][spantype].length-1])) %4==0)
	mdaynum[1]+=1;
	points[0]=data[1][spantype][0]+"-01-01";
	points[1]=data[1][spantype][data[1][spantype].length-1]+month+'-'+day;
	
	}
if(spantype==1){
	points[0]=data[1][0]+'-'+getMonthNum(data[1][spantype][0])+'-'+"01";
		points[1]=data[1][0]+'-'+getMonthNum(data[1][spantype][data[1][spantype].length-1])+'-'+getMonthDayNum(parseInt(data[1][0]),data[1][spantype][data[1][spantype].length-1]);
	
}
				
$.post("<?php echo base_url();?>reporting/clientsreport",{
	points:points
	},
	function (data){
data=data.split("<br>");
var getdata;
	var dataset={
		labels : [],
				datasets:[
				{
				fillColor : "rgba(151,187,205,0.5)",
				strokeColor : "rgba(151,187,205,0.8)",
				highlightFill : "rgba(151,187,205,0.75)",
				highlightStroke : "rgba(151,187,205,1)",
				data :[]
				}]
	}
	var numbers="";
var style="'color:red;'";
var secstyle="'color:green;float:right'";
	for(var x=0;x<data.length-1;x++){
		getdata=data[x].split(" ");
		dataset.labels.push(getdata[0]);
		dataset.datasets[0].data[x]=parseInt(getdata[1]);
		numbers+="<span><b style=" +style+">"+getdata[0]+"</b><b style="+secstyle+">"+getdata[1]+"</b></span><br>"
	}
		$("#patientdata").html(numbers);
		var ctx=document.getElementById("myChart").getContext("2d");
		var choice=document.getElementById("chartop").value;
		var chart;
		if(choice==0)
		 chart= new Chart(ctx).Bar( dataset, {responsive : true});
		else
		chart=new Chart(ctx).Line(dataset,{responsive:true});	
		$("#myChart").html(chart);
		chart.clear();
		$("#results").slideDown();
		
		});
	
			
}

}
function checkinputs(spantype){
	var check=-1;
	var inputs=new Array();
	if(spantype!="non"){
		check=0;
	var size=parseInt(spantype)+1;
	var checks=new Array("year","months");
			for(var x=0;x<size && document.getElementById(checks[x]).value!="";x++,check+=1)
					inputs[x]=document.getElementById(checks[x]).value;			
		check=check-size;
		
	}
	return (new Array(check,inputs));
}

/*function getDays(elem){
	if(elem.value<2)
		$("#days").slideUp();
				else
					$("#days").slideDown();
}*/

function populate(){
	var year=document.getElementById("year");
	var div=document.getElementById("span").value;
	var size=(div==1 && div!="non")?1:2;
	var values=limit("years",size);
	var years=values[0];
	if(div==1){
		year.value=years;
	getMonths(years);
	}
	
	else if(div==0){
			if(values[1]!="")
				years=years+" to "+values[1];
		}
		year.value=years;
	
}

function limit(id,size){
		var slots=document.getElementsByName(id);
		var temp=new Array();
		var ctr;
		var values=new Array("","");
		var x;
		for(x=0,ctr=0;x<slots.length && ctr<size;x++){
			if(slots[x].checked==true){
				temp[ctr]=x;
				values[ctr]=slots[x].value;
				ctr+=1;	
			}
		}
			if(ctr>=0){
			var toggle=(ctr<size)?false:true;
			for(var x=0,ctr=0;x<slots.length;x++){
				if(x!=temp[ctr])
						slots[x].disabled=toggle;
						else{
							if(ctr<temp.length)
									ctr+=1;
					}
			}
	}
	return values;
}

function displayYear(){
var div=document.getElementById("ScrollCB1");
if(div.style.display=="none")
		$("#ScrollCB1").slideDown();
			else
				$("#ScrollCB1").slideUp();
}

function displayMonths(){
 var div=document.getElementById("ScrollCB");
if(div.style.display=="none")
	$("#ScrollCB").slideDown();
		else
			$("#ScrollCB").slideUp();
}

function getYear(){
	var date= new Date()
	var func="'populate()'";
	var text="Select Year";
	var value="non";
	var list="";
	var checkbox="'checkbox'";
	var name="years"
	for(date=date.getFullYear();date>2006;date--)
		list+="<label><input type="+checkbox+" "+"onclick="+func+" "+"name="+name+" "+"value="+date+">"+" "+date+"</label><br>";
	$("#ScrollCB1").html(list);
}

function getMonths(years){
	var list="";
	if(years!=""){
	 var months=getArraymonths(-1);
	var date=new Date();
		var size=12;
		var func="'populatemonth()'"
		var checkbox="'checkbox'";
		var name="month"
		if(years==date.getFullYear())
			size=date.getMonth();
				for(var x=0;x<size;x++)
					list+="<label><input type="+checkbox+" "+"name="+name+" "+"onclick="+func+" "+"value="+(x+1)+" />"+" "+months[x]+"</label><br>";
	}
		$("#ScrollCB").html(list);
}

function SelectSpan(elem){
	getYear();
	
	if(elem.value<=1){
		if(elem.value==1)
			$("#months").slideDown();
		
				else{
					$("#months").slideUp();
					$("#ScrollCB").slideUp();
				}
				$("#year").slideDown();
	
	}
		else if(elem.value=="non"){
			$("#months").slideUp();
			$("#year").slideUp();
			$("#ScrollCB").slideUp();
			$("#ScrollCB1").slideUp();
		}
}

function populatemonth(){
	var div=document.getElementById("months");
	var values=limit("month",2);
		var temp="";
					if(values[0]!=""){
						temp=getArraymonths(values[0])
		if(values[1]!=""){
				if(values[0]<values[1])
					temp=temp+" to "+getArraymonths(values[1]);
						else	
							temp=getArraymonths(values[1])+" to "+temp;
			}
					}
			div.value=temp;
}
function getArraymonths(elem){
	var months=new Array("January","Febuary","March","April","May","June","July","August","September","October","November","December");	
	var temp;
	if(!isNaN(elem) && elem<1)
		temp=months;
	else if(elem=="")
		temp=elem;
	else
		temp=months[elem-1];
	
	return temp;
}

function getMonthNum(month){
var ret="01";
switch(month){
case "Febuary":ret="02";
break;
case "March":ret="03";
break;
case "April":ret="04";
break;
case "May":ret="05";
break;
case "June":ret="06";
break;
case "July":ret="07";
break;
case "August":ret="08";
break;
case "September":ret="09";
break;
case "October":ret="10";
break;
case "November":ret="11";
break;
case "December":ret="12";
break;
}
return ret;	
}
function getMonthDayNum(year,month){
var ret=31;
switch(month){
	case "Febuary":
	if(year%4==0)
		ret=29;
	else 
		ret=28;
	break;
	case "March":ret=31;
	break;
	case "April":ret=30;
	break;
	case "May":ret=31;
	break;
	case "June":ret=30;
	break;
	case "July":ret=31;
	break;
	case "August":ret=31;
	break;
	case "September":ret=30;
	break;
	case "October":ret=31;
	break;
	case "November":ret=30;
	break;
	case "December":ret=31;
	break;
}
return ret;
}
function showresult(elem){
var div=document.getElementById("patientdata");
if(div.style.display=="none")
	$("#patientdata").slideDown();
else	
	$("#patientdata").slideUp();
}
</script>
