<?php
	$this_is_page = true;

	include 'back/init.php';

	$session = new Session();

	if(!$session->user->is_authorized() || !$session->user->checkRole(['allowed', 'admin'])) {
		Framework::redirect('./login.php');
		exit(0);
	}

	$user = $session->user;
?>
<!DOCTYPE html>

<html>
	<head>
		<meta charset="UTF-8">

		<script src="front/js/jquery.js"></script>
		<script src="front/js/js.cookie.js"></script>
		
		<script src="front/js/i18next.min.js"></script>

		<script src="front/locale/locale_ru.js"></script>
		<script src="front/locale/locale_ua.js"></script>
		
		<script type="text/javascript">
			{
				var languages = {
					en: {}
					, ru: { translation: window.locale_ru }
					, ua: { translation: window.locale_ua }
				}

				var cookiePath = window.location.pathname
				cookiePath = cookiePath.substr(0, cookiePath.lastIndexOf('/') + 1)

				window.lang = Cookies.get('lang', { path: cookiePath })

				window.setLang = function(newLang) {
					window.lang = newLang
					Cookies.set('lang', window.lang, { path: cookiePath })
				}

				if(!window.lang) {
					setLang('en')
				}

				i18next.init({
					lng: window.lang
					, fallbackLng: false
					, nsSeparator: false
					, keySeparator: false
					, resources: languages
				})
				window.t = function() { return i18next.t.apply(i18next, arguments) }

				$(function(){
					$('[data-i18n]').each(function() {
						var $this = $(this)
						$this.text(t($this.data('i18n')))
					})
				})
			}
		</script>

		<title data-i18n="UKR 3W"></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="front/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="front/css/dc.css"/>

		<script src="front/js/d3.min.js"></script>
		
		<script src="front/js/crossfilter.js"></script>
		<script src="front/js/dc.js"></script>

		<script src="front/js/Blob.js"></script>
		<script src="front/js/FileSaver.js"></script>

		<script src="front/js/bootstrap.js"></script>
		<script src="front/ukr_adm1b.js"></script>
		<script src="front/NGCA_boundaries.js"></script>
		<style>
			body {
				margin-bottom: 50px;
				background-color: #ffffff;
			}
			div {
				color: #026cb6;
			}
			h7 {
				color: #58585a;
			}
			h8 {
				color: #58585a;
				font-size: 40px;
			}
			.appliedfilters {
				background: #f1f1f1;
				padding: 5px 5px 0 0;
				color: #58585a;
				vertical-align: baseline;
				margin-bottom: 10px;
			}
			.appliedfilters .appliedfilters-mark {
				color: #58585a;
				display: inline-block;
				padding: 5px 0;
				margin: 0 0 5px 5px;
			}
			.appliedfilters ul {
				display: inline-block;
				list-style-type: none;
				padding: 0;
				margin: 0;
			}
			.appliedfilters li {
				display: inline-block;
				background: #f9f9f9;
				padding: 5px 7px;
				margin: 0 0 5px 5px;
			}

			a {
				text-decoration: underline;
			}
			a.btn, .navbar a {
				text-decoration: none;
			}

			.lang-nav {
				padding-left: 12px;
				padding-right: 12px;
			}
			.lang-nav .lang.navbar-text {
				margin-left: 3px;
				margin-right: 3px;
			}
			.lang-nav .lang a {
				padding-left: 3px;
				padding-right: 3px;
			}
			.lang-nav .lang-active {
				opacity: .6;
				font-weight: bold;
			}

			.dc-chart .deselected path {
				fill-opacity: 1;
				fill: #dddddd;
			}

			.navbar-main {
				min-height: 50px;
			}
			.navbar-main .navbar-brand {
				font-size: 18px;
				height: 50px;
				line-height: 20px;
				padding: 15px;
			}


			.navbar-first {
				margin-bottom: 0;
				background-color: #222;
				min-height: 50px;
			}
			.navbar-first .navbar-brand {
				padding: 15px;
				height: 50px;
				line-height: 20px;
				font-size: 18px;
			}
		</style>
	</head>
	<body>


		<nav class="navbar navbar-inverse navbar-static-top navbar-first">
			<div class="container">
				<span class="navbar-brand <?= $nav == 'alert_management' ? 'active' : '' ?>">Ukraine 3W Internal</span>
				<ul class="nav navbar-nav navbar-right">
					<?php if(isset($user) && $user->is_authorized()) : ?>
						<?php if($user->role == 'admin') : ?>
							<li class="<?= $nav == 'user_management' ? 'active' : '' ?>"><a href="./listuser.php">User management</a></li>
						<?php endif; ?>
						<li><a href="./logout.php">Logout</a></li>
					<?php else : ?>
						<li class="<?= $nav == 'login' ? 'active' : '' ?>"><a href="./login.php">Login</a></li>
					<?php endif; ?>
				</ul>
			</div>
		</nav>



		<nav class="navbar navbar-inverse navbar-static-top navbar-main">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only" data-i18n="Toggle navigation"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php" data-i18n="UKRAINE - 3W Dashboard"></a>
				</div><!--/.nav-collapse -->
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								<span data-i18n="CLUSTER"></span>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu" role="menu">
								
								<!-- <li><a href="index_shelter.html">SHELTER - NFI</a></li> -->
								<!-- <li><a href="index_wash.html">WASH</a></li> -->
								<li><a href="index_education.php" data-i18n="EDUCATION"></a></li>
								<!-- <li><a href="index_health.html">HEALTH & NUTRITION</a></li> -->
								<!-- <li><a href="index_food.html">FOOD SECURITY</a></li> -->
								<!-- <li><a href="index_logistic.html">LOGISTIC</a></li> -->
								<!-- <li><a href="index_protection.html">PROTECTION</a></li> -->
								<!-- <li><a href="index_livelihoods.html">LIVELIHOODS</a></li> -->
							</ul>
						</li>
						
						<li class="dropdown" align= "left">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
								<span data-i18n="OBLAST"></span>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="index_oblast.php" data-i18n="Donetska-Luhanska"></a></li>
							</ul>
						</li>
						
					</ul>
					<ul class="nav navbar-nav navbar-right lang-nav" >
						<li class="lang js-langswitch-en"><a href="javascript:setLang('en');location.reload();" data-i18n="EN"></a></li>
						<li class="lang js-langswitch-ua"><a href="javascript:setLang('ua');location.reload();" data-i18n="UA"></a></li>
						<li class="lang js-langswitch-ru"><a href="javascript:setLang('ru');location.reload();" data-i18n="RU"></a></li>
						<script>$('.js-langswitch-' + lang).addClass('lang-active')</script>
					</ul>
					<ul class="nav navbar-nav navbar-right" >
						<li><a href="#"> </a></li>
						
						<li ><a class="btn btn-primary btn-sm" id="data" href="index.php" data-i18n="Homepage"></a></li>
					</ul>
					<!--
					<ul class="nav navbar-nav navbar-right">
						<li style="top:12px"><img src="images/REACH_logo.png" width="200" height="50"style="border-width:0px;"><li>
					</ul>
					-->
				</div><!--/.nav-collapse -->
			</div><!--/.container -->
		</nav>

		<div class="container">
			<div class="row" align="justify">
				<div class="col-md-12" >
					<h7><p data-i18n="In response to the humanitarian crisis in Ukraine countless volunteers, as well as many international humanitarian actors, including the United Nations system, national and international NGOs, and other international organizations, are engaged in the humanitarian relief effort. In December 2014 the cluster system was activated, with 8 clusters engaging in life-saving activities throughout the country. This 3W (WHO is doing WHAT WHERE) captures the presence of partners currently on the ground in each oblast.">
					</p></h7>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6" align="left">
					<h4 data-i18n="Data from January 2016 to February 2016"></h4>
				</div>
				<div id="count-info" class="col-md-5" align="right">
					<h4>
						<span class="filter-count"></span>
						<span data-i18n="Activities selected of"></span>
						<span class="total-count"></span></h4>
				</div>
				<div class="col-md-1" align="right">
					<a class="reset btn btn-primary btn-sm" id="reset" href="javascript:dc.filterAll();dc.redrawAll();" data-i18n="Reset All"></a>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12" >
					<p>
						<a href="javascript:saveData()" data-i18n="Download dataset"></a>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="appliedfilters">
						<div class="appliedfilters-mark" data-i18n="Currently data displayed for filters:"></div>
						<ul id="appliedFilters"></ul>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8" id="map"></div>

				<div class="col-md-4" id="oblast">
					<h4 data-i18n="Filter by Oblast"></h4>
				</div>
			</div> 
			<div class="row">
				<div class="col-md-4">
					<h4 data-i18n="Filter by Status"></h4>
					<div id="status" class="row"></div>
				</div>
				<div class="col-md-4">
					<h4 data-i18n="Filter by Cluster"></h4>
					<div id="cluster" class="col-md-12"></div>
				</div>
				<div class="col-md-4">
				<h4 data-i18n="Filter by GCA / NGCA"></h4>
					<div id="area" class="row"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6" align="left">
				</div>
				<div id="count-info" class="col-md-5" align="right">
					<h4><span class="filter-count"></span><span class="total-count"></span> </h4>
				</div>
				<div class="col-md-1" align="right">
					<a class="reset btn btn-primary btn-sm" id="reset" href="javascript:dc.filterAll();dc.redrawAll();" data-i18n="Reset All"></a>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<h4 data-i18n="Filter by Activity"></h4>
					<div id="activity" class="col-md-12"></div>
				</div>
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-6">
							<h4 data-i18n="Filter by Agency (Top 20)"></h4>
							<div id="agency" class="col-md-12"></div>
						</div>
						<div class="col-md-6">
							<h4 data-i18n="Filter by Implementing Partner (Top 20)"></h4>
							<div id="partner" class="col-md-12"></div>
						</div>

						<div class="col-md-6">
							<h4 data-i18n="Number of activities reported by month"></h4>
							<h5 data-i18n="Data as of"></h5>
							<div id="dataasof" class="row"></div>
						</div>
						<div class="col-md-6">
							<h4 data-i18n="Number of activities reported by month"></h4>
							<h5 data-i18n="Start data activity"></h5>
							<div id="date" class="row"></div>
						</div>
					</div>
				</div>
				<!--<div class="col-md-4">
					<h4>Filter by Donor (Top 20)</h4>
					<div id="donor" class="col-md-12"></div>
				</div>  -->
			</div>
			<div class="row">
				<div class="col-md-6" align="left">
				</div>
				
				<div id="count-info1" class="col-md-5" align="right">
					<h4><span class="filter-count"></span><span class="total-count"></span></h4>
				</div>

				<div class="col-md-1" align="right">
					<a class="reset btn btn-primary btn-sm" id="reset" href="javascript:dc.filterAll();dc.redrawAll();" data-i18n="Reset All"></a>
				</div>
			</div>
			<div class="row">
				<!--
				<div class="col-md-4">
					<h4>Number of households Beneficiaries by Oblast</h4>
					<div id="benefhh" class="row"></div>
				</div>
				<div class="col-md-4">
					<h4>Number of individuals Beneficiaries by Oblast</h4>
					<div id="benefind" class="row"></div>
				</div>
				-->
			</div>
			<!--
			<div class="row">
				<div class="col-md-6" align="left">
				</div>

				<div id="count-info1" class="col-md-5" align="right">
					<h4><span class="filter-count"></span><span class="total-count"></span> </h4>
				</div>

				<div class="col-md-1" align="right">
					<a class="reset btn btn-primary btn-sm" id="reset" href="javascript:dc.filterAll();dc.redrawAll();">Reset All</a>
				</div>
			</div>
			-->
			<div class="row">
				<div class="col-md-6">
					<h4></h4>
					<div id="temp" class="col-md-12"></div>
				</div>
			</div>
			<!--
			<div class="row">
				<div class="col-md-6">
					<h4></h4>
					<div id="temp" class="col-md-12"></div>
				</div>
			</div>
			-->
			<div class="row">
				<h8>_________________________________________________</h8> 
			</div>
			<div class="row">
				<h4 data-i18n="Developed by :"></h4>
			</div>
			<div class='row'>
				<img  src="front/images/REACH_title10.png" alt="Reach Logo" width="250px"/><br>
			</div>
		</div>
	<script type="text/javascript">
		var filters = {}
		filters[t('Data As Of')] = []
		filters[t('Activity')] = []
		filters[t('Agency')] = []
		filters[t('Area')] = []
		filters[t('Cluster')] = []
		filters[t('Map')] = []
		filters[t('Oblast')] = []
		filters[t('Partner')] = []
		filters[t('Start')] = []
		filters[t('Status')] = []

		var $appliedFilters = $('#appliedFilters')
		var updateFilters = function(filter) {
			$.extend(filters, filter)
			
			var $fragment = $(document.createDocumentFragment())

			for(var name in filters) {
				if(filters[name] && filters[name].length > 0) {
					$fragment.append('<li>' + name + ": " + filters[name].join(', ') + '</li>')
				}
			}

			$appliedFilters.html($fragment)
		}

		var projection = d3.geo.mercator().center([36,48]).scale(1900)



		var mapChart = dc.geoChoroplethChart("#map")
			//, smallsuboffmapChart = dc.geoChoroplethChart("#map1")
			//, distmapChart = dc.geoChoroplethChart("#map1")
			//, timeChart = dc.lineChart("#temp")
			, oblast_chart = dc.rowChart("#oblast")
			, cluster_chart = dc.rowChart("#cluster")
			, agency_chart = dc.rowChart("#agency")
			, partner_chart = dc.rowChart("#partner")
			//, donor_chart = dc.rowChart("#donor")
			, status_chart = dc.barChart("#status")
			, activity_chart = dc.rowChart("#activity")
			//, dataTable = dc.dataTable("#dc-table-graph")
			, area_chart = dc.barChart("#area")
			//, raion_chart = dc.rowChart("#raion")
			, benefhh_chart = dc.rowChart("#benefhh")
			, benefind_chart = dc.rowChart("#benefind")
			, startChart = dc.barChart("#date")
			, dataasofChart = dc.barChart("#dataasof")


		var load = function(dataToLoad, callback) {
			$.each(dataToLoad, function(name, item) {
				item.state = 'pending'

				item.loader(item.url, function(error, data) {
					item.state = error ? 'error' : 'done'
					item.data = error ? error : data

					for(var name in dataToLoad) {
						if(dataToLoad[name].state == 'pending') { return }
					}

					callback(dataToLoad)
				})
			})
		}

		var dataToLoad = {
			data: {loader: d3.csv, url: 'front/data/Master_Table_Data_January_February_2016_v2.csv.php'}
			//, NGCABoundaries: {loader: d3.json, url: 'NGCA_boundaries.js'}
		}

		load(dataToLoad, function(result) {
			var data = result.data.data
			//var NGCABoundaries = result.NGCABoundaries//.data

			// var debutFormat = d3.time.format("%d.%m.%Y");
			// var dateFormat = d3.time.format('%m.%y');
			var dateFormat = d3.time.format('%d-%b-%y');

			data.forEach(function (d) {
				d.dd = dateFormat.parse(d['ACTIVITY_START']);
				d.month = d.dd ? d3.time.month(d.dd) : null; // pre-calculate month for better performance
				//d.close = +d.close; // coerce to number
				//d.open = +d.open;
			});
			
			var cf = crossfilter(data)

			// var distMapDim     = cf.dimension(function(d) {return d['ADMIN2_ID'];})
			// 	, distMapGroup = distMapDim.group();

			// var oblastNames = {
			// 	id100000000: "Avtonomna Respublika Krym"
			// 	, id500000000: "Vinnytska Oblas"
			// 	, id700000000: "Volynska Oblast"
			// 	, id1200000000: "Dnipropetrovska Oblast"
			// 	, id1400000000: "Donetska Oblast"
			// 	, id1800000000: "Zhytomyrska Oblast"
			// 	, id2100000000: "Zakarpatska Oblast"
			// 	, id2300000000: "Zaporizska Oblast"
			// 	, id2600000000: "Ivano-Frankivska Oblast"
			// 	, id3200000000: "Kyivska Oblast"
			// 	, id3500000000: "Kirovohradska"
			// 	, id4400000000: "Luhanska Oblast"
			// 	, id4600000000: "Lvivska Oblast"
			// 	, id4800000000: "Mykolaivska Oblast"
			// 	, id5100000000: "Odeska Oblast"
			// 	, id5300000000: "Poltavska Oblast"
			// 	, id5600000000: "Rivnenska Oblast"
			// 	, id5900000000: "Sumska Oblast"
			// 	, id6100000000: "Ternopilska Oblast"
			// 	, id6300000000: "Kharkivska Oblast"
			// 	, id6500000000: "Khersonska Oblast"
			// 	, id6800000000: "Khmelnytska Oblast"
			// 	, id7100000000: "Cherkaska Oblast"
			// 	, id7300000000: "Chernivetska Oblast"
			// 	, id7400000000: "Chernihivska Oblast"
			// 	, id8000000000: "Kyiv"
			// 	, id8500000000: "Sevastopol"
			// 	, na: "NA"
			// }

			// cf.oblast = cf.dimension(function(d){ return oblastNames['id' + d.ADMIN1_ID] || oblastNames['na'] })

			cf.oblast = cf.dimension(function(d) {return d['ADMIN1_NAME_ENG'] })

			cf.agency = cf.dimension(function(d){ return d['org_name'] })
			cf.partner = cf.dimension(function(d){ return d['partner #1_name'] })
			cf.start = cf.dimension(function(d) { return d['month'] })
			var dataAsOfFormat = d3.time.format("%B %Y")
			cf.dataasof = cf.dimension(function(d) { return dataAsOfFormat(dateFormat.parse(d['Date'])) })
			
			
			cf.status = cf.dimension(function(d){ return d['status_name'] })
			// cf.benefhh = cf.dimension(function(d){ return d.BENEF_HH })

			// cf.benefind = cf.dimension(function(d){ return d.BENEF_IND })
			cf.cluster = cf.dimension(function(d){ return d['cluster_name'] })
			// cf.donor = cf.dimension(function(d){ return d.DONOR })
			cf.activity = cf.dimension(function(d){ return d['ACTIVITY'] })

			cf.area = cf.dimension(function(d){ return d['area_type'] || t('Not specified') });
			
			//cf.raion = cf.dimension(function(d){ return d.ADMIN2_ID; });
			
			//cf.Annee2014 = cf.dimension(function(d){ return d.START_ACTIVITY; });
			//cf.Annee2015 = cf.dimension(function(d){ return d.END_ACTIVITY; });

			var oblast = cf.oblast.group()
				.reduceCount(function(d) { return d['ADMIN1_NAME_ENG'] });
			var cluster = cf.cluster.group()
				.reduceCount(function(d) { return d['CLUSTER_ID'] }) // counts 
			// var donor = cf.donor.group()
			// 	.reduceCount(function(d) { return d.DONOR; }); // counts 
			
			var area = cf.area.group()
				.reduceCount(function(d) { return d['area_type'] }) // counts 
			
			var agency = cf.agency.group()
				.reduceCount(function(d) { return d['AGENCY'] })

			var partner = cf.partner.group()
				.reduceCount(function(d) { return d['PARTNER'] })
			var activity = cf.activity.group();
			var benefhh = cf.oblast.group()
				.reduceSum(function(d) { return d['BENEF_HH'] })// sum
			var benefind = cf.oblast.group()
				.reduceSum(function(d) { return d['BENEF_IND'] })// sum
			var status = cf.status.group();
			//var raion = cf.raion.group();
			var startGroup = cf.start.group()
				.reduceCount(function(d) { return d['month'] })
			var dataasofGroup = cf.dataasof.group()
				.reduceCount(function(d) { return d['DATA_AS_OF'] })
			//var A2014Group = cf.Annee2014.group();
			//var A2015Group = cf.Annee2015.group();
			
			
			var all = cf.groupAll();

			window.saveData = function() {
				var header = [
					'Date'
					, 'cluster_name'
					, 'org_name'
					, 'partner #1_name'
					, 'partner #2_name'
					, 'ADMIN1_NAME_ENG'
					, 'ADMIN2_NAME_UKR'
					, 'area_type'
					, 'status_name'
					, 'ACTIVITY'
					, 'ACTIVITY_START'
					, 'ACTIVITY_END'
					, 'NUMBER_REACHED'
				]

				var data = cf.dataasof.top(Infinity)

				data = data.map(function(record) {
					return [
						record['Date']
						, record['cluster_name']
						, record['org_name']
						, record['partner #1_name']
						, record['partner #2_name']
						, record['ADMIN1_NAME_ENG']
						, record['ADMIN2_NAME_UKR']
						, record['area_type']
						, record['status_name']
						, record['ACTIVITY']
						, record['ACTIVITY_START']
						, record['ACTIVITY_END']
						, record['NUMBER_REACHED']
					]
				})

				data.unshift(header)

				var res = d3.csv.formatRows(data)

				var blob = new Blob([res], {type: "text/csv;charset=utf-8"})

				saveAs(blob, 'ukraine-3w-dataset-' + (new Date()).getTime() + '.csv')
			}


			mapChart.width(700)
				.height(450)

				.dimension(cf.oblast)
				.group(oblast)
				.colors(['#dddddd', '#C6DBEF', '#9ECAE1', '#6BAEF1', '#4292C6', '#2171B5', '#084594'])
				.colorDomain([0, 6])
				.colorAccessor(function(d){
					if (d=="undefined") {return 0}
					else if ((d>0) & (d<=250)) {return 1}
					else if ((d>251) & (d<=500)) {return 2}
					else if ((d>501) & (d<=1000)) {return 3}
					else if ((d>1001) & (d<=2000)) {return 4}
					else if ((d>2001) & (d<=4000)) {return 5}
					else if (d>4000) {return 6}
					else { return 0 }
				})
				.overlayGeoJson(
					adm1.features.filter(function(feature) {
						// dont show Sevastopol and AR Krym in chart. It's drowind separately at the end of script.
						return feature.properties.KOATUU != "8500000000" && feature.properties.KOATUU != "0100000000"
					})
					, "state"
					, function (d) {
						return d.properties.NAME_LAT;
					}
				)
				.projection(projection)
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Map')] = mapChart.filters()
					updateFilters(filters)
				})


			/*
			distmapChart.width(500)
				.height(400)
				.dimension(distMapDim)
				.group(distMapGroup)
				.colors(d3.scale.quantize().range(["#E54143", "#C7C9CB",  "#464749"]))
				.colorDomain([0, 200])
				.colorCalculator(function (d) { return d ? mapChart.colors()(d) : '#ccc'; })
				.overlayGeoJson(adm2.features, "state", function (d) {
					return d.properties.ADM2_PCODE;
				})
				.projection(d3.geo.mercator().center([40,47]).scale(1500));


				smallsuboffmapChart.width(500)
				.height(400)
				.dimension(SubOffMapDim)
				.group(SubOffMapGroup)
				.colors(d3.scale.quantize().range(["#E54143", "#C7C9CB",  "#464749"]))
				.colorDomain([0, 200])
				.colorCalculator(function (d) { return d ? mapChart.colors()(d) : '#ccc'; })
				.overlayGeoJson(adm1.features, "state", function (d) {
					return d.properties.ADM1_PCODE;
				})
				.projection(d3.geo.mercator().center([40,47]).scale(1500));
			*/
			
			cluster_chart.width(300)
				.height(350)
				.margins({top: 10, right: 10, bottom: 40, left: 40})
				.dimension(cf.cluster)
				.group(cluster)
				.elasticX(true)
				.data(function(group) {
					return group.top(10);
				})
				.colors(['#026CB6'])
				.colorDomain([0,6])
				.colorAccessor(function(d, i){return i%6;})
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Cluster')] = cluster_chart.filters()
					updateFilters(filters)
				})
				.xAxis().ticks(4)
			
			agency_chart.width(300)
				.height(450)
				.margins({top: 10, right: 10, bottom: 40, left: 40})
				.dimension(cf.agency)
				.group(agency)
				.elasticX(true)
				.data(function(group) {
					return group.top(20);
				})
				.colors(['#026CB6'])
				.colorDomain([0,1])
				.colorAccessor(function(d, i){return i%1;})
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Agency')] = agency_chart.filters()
					updateFilters(filters)
				})
				.xAxis().ticks(4)
			
			
			partner_chart.width(300)
				.height(450)
				.margins({top: 10, right: 10, bottom: 40, left: 40})
				.dimension(cf.partner)
				.group(partner)
				.data(function(group) {console.dir(group); return group.all()})
				.elasticX(true)
				.data(function(group) {
					return group.top(Infinity).filter(function(element) {return !!element.key}).slice(0,20)
				})
				.colors(['#026CB6'])
				.colorDomain([0,1])
				.colorAccessor(function(d, i){return i%1;})
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Partner')] = partner_chart.filters()
					updateFilters(filters)
				})
				.xAxis().ticks(4)

			/*
			donor_chart.width(300)
				.height(350)
				.margins({top: 10, right: 10, bottom: 40, left: 40})
				.dimension(cf.donor)
				.group(donor)
				.elasticX(true)
				.data(function(group) {
					return group.top(20);
				})
				.colors(['#E54143'])
				.colorDomain([0,6])
				.colorAccessor(function(d, i){return i%6;})
				.xAxis().ticks(5);
			*/
			
			benefhh_chart.width(300)
				.height(500)
				.margins({top: 10, right: 10, bottom: 40, left: 40})
				.dimension(cf.oblast)
				.group(benefhh)
				.elasticX(true)
				.data(function(group) {
					return group.top(28);
				})
				.colors(['#026CB6'])
				.colorDomain([0,2])
				.colorAccessor(function(d, i){return i%2;})
				.xAxis().ticks(4);


			benefind_chart.width(300)
				.height(500)
				.margins({top: 10, right: 10, bottom: 40, left: 40})
				.dimension(cf.oblast)
				.group(benefind)
				.elasticX(true)
				.data(function(group) {
					return group.top(28);
				})
				.colors(['#026CB6'])
				.colorDomain([0,2])
				.colorAccessor(function(d, i){return i%2;})
				.xAxis().ticks(4);
			
			status_chart.width(300)
				.height(350)
				.margins({top: 10, right: 10, bottom: 35, left: 40})
				.dimension(cf.status)
				.group(status)
				.transitionDuration(500)
				.centerBar(false)
				.gap(40) // 65 = norm
				.colors("#026CB6")
				.x(d3.scale.ordinal().domain(["Completed", "Ongoing", "Planned", "Suspended"]))
				.xUnits(dc.units.ordinal)
				.elasticY(true)
				.xAxisLabel(t('Status'))
				.yAxisLabel(t('# of activities by status'))
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Status')] = status_chart.filters()
					updateFilters(filters)
				})
				.xAxis().tickFormat()

			area_chart.width(300)
				.height(350)
				.margins({top: 10, right: 10, bottom: 35, left: 40})
				.dimension(cf.area)
				.group(area)
				.transitionDuration(500)
				.centerBar(false)
				.gap(40)  // 65 = norm
				.colors("#026CB6")
				.x(d3.scale.ordinal().domain(["GCA", "NGCA", t('Not specified')]))
				.xUnits(dc.units.ordinal)
				.elasticY(true)
				.xAxisLabel(t('Area type'))
				.yAxisLabel(t('# of activities by area'))
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Area')] = area_chart.filters()
					updateFilters(filters)
				})
				.xAxis().tickFormat()


			/*
			Annee2014_chart
				.dimension(cf.Annee2014)
				.group(A2014Group)
				.width(65).height(80)
				.innerRadius(10)
				.colors(d3.scale.ordinal().range(  [ '#999999', '#FF6600']));

			Annee2015_chart
				.dimension(cf.Annee2015)
				.group(A2015Group)
				.width(65).height(80)
				.innerRadius(10)
				.colors(d3.scale.ordinal().range(  [ '#999999', '#FF6600']));
			*/


			activity_chart.width(300).height(activity.size() * 20 + 55)
				.margins({top: 10, left: 10, right: 40, bottom: 40})
				.dimension(cf.activity)
				.group(activity)
				.ordering(function(d){ return -d.value })
				.elasticX(true)
				.colors(['#026CB6'])
				.colorDomain([0,8])
				.colorAccessor(function(d, i){return i%8;})
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Activity')] = activity_chart.filters()
					updateFilters(filters)
				})
				.xAxis().ticks(4)

			oblast_chart.width(300).height(500)
				.margins({top: 10, left: 10, right: 40, bottom: 40})
				.dimension(cf.oblast)
				.group(oblast)
				.elasticX(true)
				.data(function(group) {
					return group.top(28);
				})
				.colors(['#026CB6'])
				.colorDomain([0,6])
				.colorAccessor(function(d, i){return i%6;})
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Oblast')] = oblast_chart.filters()
					updateFilters(filters)
				})
				.xAxis().ticks(4)

			/*
			timeChart.width(500)
				.height(200)
				.margins({top: 10, right: 40, bottom: 40, left: 40})
				.dimension(cf.start)
				.group(startGroup)
				.transitionDuration(10000000)
				.elasticY(true)
				.x(d3.time.scale().domain([new Date(2016, 1, 1), new Date(2016, 2, 1)]))
				.xAxisLabel('Date')
				.yAxisLabel('# of activities by date');
			*/

			startChart.width(350)
				.height(220)
				.margins({top: 30, right: 10, bottom: 40, left: 40})
				.dimension(cf.start)
				.group(startGroup)
				.transitionDuration(500)
				.centerBar(true)
				.colors("#026CB6")
				.gap(65)  // 65 = norm
				// .filter([3, 5])
				.x(d3.time.scale().domain([new Date(2013, 12, 1), new Date(2016, 6, 1)]))
				.elasticY(true)
				.xAxisLabel(t('Date'))
				.yAxisLabel(t('# of activities by date'))
				.on('filtered', function(event, filter) {
					var res = filter

					if(res && res.length > 0) {
						var res = [dateFormat(res[0]) + "&ndash;" + dateFormat(res[1])]
					}
					var filters = {}
					filters[t('Start')] = oblast_chart.filters()
					updateFilters(filters)
				})
				.xAxis().ticks(4)
				//.xAxis().tickFormat()

			dataasofChart.width(350)
				.height(220)
				.margins({top: 30, right: 10, bottom: 40, left: 40})
				.dimension(cf.dataasof)
				.group(dataasofGroup)
				.transitionDuration(500)
				.centerBar(false)
				.colors("#026CB6")
				.gap(50)  // 65 = norm
				// .filter([3, 5])
				.x(d3.scale.ordinal()) // .domain(["January 2016","February 2016"]))
				.xUnits(dc.units.ordinal)
				.elasticY(true)
				.xAxisLabel(t('Month'))
				.yAxisLabel(t('# of activities by date'))
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Data As Of')] = dataasofChart.filters()
					updateFilters(filters)
				})
				.xAxis().tickFormat()


			/*
			status_chart.width(300)
				.height(350)
				.margins({top: 10, right: 10, bottom: 35, left: 40})
				.dimension(cf.status)
				.group(status)
				.transitionDuration(500)
				.centerBar(false)
				.gap(40) // 65 = norm
				.colors("#E54143")
				.x(d3.scale.ordinal().domain(["Completed", "Ongoing", "Planned", "Suspended"]))
				.xUnits(dc.units.ordinal)
				.elasticY(true)
				.xAxisLabel('Status')
				.yAxisLabel('# of activities by status')
				.xAxis().tickFormat();
			*/


			/*
			raion_chart.width(250).height(1250)
				.margins({top: 0, left: 10, right: 0, bottom: 40})
				.dimension(cf.commune)
				.group(commune)
				.elasticX(true)
				.data(function(group) {
					return group.top(80);
				})
				.colors(['#7f7f7f', '#bc9d22', '#17becf', '#427f0e', '#9467bd', '#c49c94', '#8c564b', '#fcbd22', '#9e9ac8', '#7f7f7f', '#bcbd22', '#17becf', '#ff7f0e'])
				.colorDomain([0,12])
				.colorAccessor(function(d, i){return i%12;})
				.xAxis().ticks(5);
			*/


			/*
			dataTable.width(800).height(800)
				.dimension(cf.oblast)
				.group(function(d) { return "" })
				.size(200)
				.columns([
					function(d) { return d.CLUSTER_ID; },
					function(d) { return d.AGENCY; },
					function(d) { return d.PARTNER; },
					function(d) { return d.ADMIN1_ID; },
					function(d) { return d.ADMIN2_ID; },
					// function(d) { return d.STATUS_ACTIVITY; },
					// function(d) { return d.START_ACTIVITY; },
					function(d) { return d.END_ACTIVITY; },
					function(d) { return d.BENEFICIARIES_HH; },
					function(d) { return d.BENEFICIARIES_IND; },
					function(d) { return '<a target="_blank" href=cartes/2014/'+d.carte2014+">PDF</a>"},
					function(d) { return '<a target="_blank" href=cartes/2015/'+d.carte2015+">PDF</a>"},
					function(d) { return '<a href=\"http://www.openstreetmap.org/?mlat=' + d.Latitude + '&mlon=' + d.Longitude +'&zoom=10'+ "\" target=\"_blank\">OSM Map</a>"},
					function(d) { return '<a href=\"http://maps.google.com/maps?m=10000&t=m&q=loc:' + d.Latitude + '+' + d.Longitude +"\" target=\"_blank\">Google Map</a>"},
				])
				.sortBy(function(d){ return d.CLUSTER_ID; })
				.order(d3.ascending);
			*/
			
			dc.dataCount("#count-info")
				.dimension(cf)
				.group(all);

			dc.renderAll();


			var g = d3.selectAll("#oblast").select("svg").append("g");
			g.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 150).attr("y", 495).text(t('# of activities by Oblast'))

			var g1 = d3.selectAll("#agency").select("svg").append("g");
			g1.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 160).attr("y", 440).text(t('# of activities by agency'))
			
			var g2 = d3.selectAll("#partner").select("svg").append("g");
			g2.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 160).attr("y", 440).text(t('# of activities by partner'))

			var g3 = d3.selectAll("#donor").select("svg").append("g");
			g3.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 160).attr("y", 340).text(t('# of activities by donor'))

			var g4 = d3.selectAll("#activity").select("svg").append("g");
			g4.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 140).attr("y", activity_chart.height() - 10).text(t('# of activities by type'))

			var g5 = d3.selectAll("#benefhh").select("svg").append("g");
			g5.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 160).attr("y", 490).text(t('# of hh beneficiairies'))
			
			var g6 = d3.selectAll("#benefind").select("svg").append("g");
			g6.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 160).attr("y", 490).text(t('# of individuals beneficiaries'))
			
			var g7 = d3.selectAll("#cluster").select("svg").append("g");
			g7.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 160).attr("y", 340).text(t('# of activities reported'))





			{
				var mapSVG = d3.select("#map svg")
					, container = mapSVG.append("g")

				var color = "#e01f22"

				mapSVG
					.append("defs")
						.append("pattern")
							.attr("id", "diagonalHatch")
							.attr("patternUnits", "userSpaceOnUse")
							.attr("width", 5)
							.attr("height", 5)
							.append("path")
								.attr("d", "M -1,1 l 2,-2    M 0,5 l 5,-5    M 4,6 l 2,-2")
								.attr("stroke", color)
								.attr("stroke-width", 1);

				container.selectAll("path")
					.data(NGCABoundaries.features)
					.enter().append("path")
						.attr("d", d3.geo.path().projection(projection))
						.attr("fill", "url(#diagonalHatch)")
						.attr("stroke", color)
						.attr("stroke-width", "1px")
						.attr("pointer-events", "none")
			}


			{
				var legend = d3.select("#map svg").append("g");


				var legendDataset = [
					{x: 15, y: 280, type: "title", text: t('Number of activities') }
					, {x: 15, y: 300, type: "label", width: 30, height: 14, spacing: 5, fill: "#C6DBEF", text:"0-250"}
					, {x: 15, y: 320, type: "label", width: 30, height: 14, spacing: 5, fill: "#9ECAE1", text:"251-500"}
					, {x: 15, y: 340, type: "label", width: 30, height: 14, spacing: 5, fill: "#6BAEF1", text:"501-1000"}
					, {x: 15, y: 360, type: "label", width: 30, height: 14, spacing: 5, fill: "#4292C6", text:"1001-2000"}
					, {x: 15, y: 380, type: "label", width: 30, height: 14, spacing: 5, fill: "#2171B5", text:"2001-4000"}
					, {x: 15, y: 400, type: "label", width: 30, height: 14, spacing: 5, fill: "#084594", text:"> 4000"}
					, {x: 15, y: 421, type: "divider", width: 110, height: 1, fill: "#cccccc"}
					, {x: 15.5, y: 430.5, type: "label", width: 29, height: 13, spacing: 5, fill: "url('#diagonalHatch')", text:"NGCA", stroke: "#e01f22"}
				]
				

				legend.selectAll("text")
					.data(legendDataset.filter(function(d) { return d.type == "title" || d.type == "label" }))
					.enter()
					.append("text")
						.attr("font-size", "12px")
						.attr("stroke", "#555555")
						.attr("dominant-baseline", "text-before-edge")
						.text(function(d) { return d.text })
						.attr("x", function(d) {
							return d.type == "label" ? d.x + d.width + d.spacing : d.x;
						})
						.attr("y", function(d) { return d.y })

				legend.selectAll("rect")
					.data(legendDataset.filter(function(d) { return d.type == "label" || d.type == "divider" }))
					.enter()
					.append("rect")
						.attr("x", function(d) { return d.x })
						.attr("y", function(d) { return d.y })
						.attr("width", function(d) { return d.width })
						.attr("height", function(d) { return d.height })
						.attr("style", function(d) {
							var res = [
								'fill:' + d.fill
							]

							if(d.stroke) {
								res.push("stroke:" + d.stroke)
								res.push("stroke-width: 1px")
								res.push("stroke-style: solid")
							}

							return res.join(';')
						})
			}

			{
				// draw border of sevastopol and AR Krym separately
				d3.select('#map svg').append('g')
					.selectAll('path')
					.data(adm1.features.filter(function(feature) {
						return feature.properties.KOATUU == "8500000000" || feature.properties.KOATUU == "0100000000"
					}))
					.enter().append("path")
						.attr("d", d3.geo.path().projection(projection))
						.attr("fill", "none")
						.attr("stroke", '#000000')
						.attr("stroke-width", "1px")

			}

			{
				// fix interactions between map and oblast charts

				var all = dc.chartRegistry.list()

				mapChart.onClick = function(datum, layerIndex) {
					var selectedRegion = mapChart.geoJsons()[layerIndex].keyAccessor(datum);

					oblast_chart.filter(selectedRegion)

					mapChart.redrawGroup()
				}

				mapChart.hasFilter = function(filter) {
					var filters = oblast_chart.filters()
					if(!filter) { return filters.length > 0}
					return filters.indexOf(filter) != -1
				}
			}

		})
	</script>
</body>
</html>
