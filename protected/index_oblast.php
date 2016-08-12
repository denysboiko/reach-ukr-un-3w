<?php
	$this_is_page = true;

	include 'back/init.php';

	$session = new Session();

	if(!$session->user->is_authorized() || !$session->user->checkRole(['editor', 'admin'])) {
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

		<title data-i18n="UKR 3W Oblast"></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="front/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="front/css/dc.css"/>

		<script src="front/js/d3.min.js"></script>

		<script src="front/js/crossfilter.js"></script>
		<script src="front/js/dc.js"></script>

		<script src="front/js/Blob.js"></script>
		<script src="front/js/FileSaver.js"></script>

		<script src="front/js/bootstrap.js"></script>
		<script src="front/ukr_adm1_oblast.js"></script>
		<script src="front/ukr_adm2_raion.js"></script>
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

			.dc-chart g.state path {
				stroke-width: 0.5px;
			}
			
			#oblast {
				position: absolute;
				z-index: 1;
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
					<a class="navbar-brand" href="index_oblast.php" data-i18n="UKRAINE - 3W Dashboard - Oblast"></a>
				</div>
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
						<li><a class="btn btn-primary btn-sm" id="data" href="index.php" data-i18n="Homepage"></a></li>
					</ul>

					<!--
					<ul class="nav navbar-nav navbar-right">
						<li style="top:12px"><img src="images/REACH_logo.png" width="200" height="50"style="border-width:0px;"><li>
					</ul>
					-->
				</div><!--/.nav-collapse -->
			</div>
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
						<span class="total-count"></span>
					</h4>
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
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-12" >
							<div id="oblast">
								<h4 data-i18n="Filter by Oblast"></h4>
							</div>
						</div>
						<div class="col-md-12" id="map1">
						</div>
					</div>
				</div>
				
				<div class="col-md-2.5" id="raion">
					<h4 data-i18n="Donetska Oblast"></h4>
					<h4 data-i18n="Filter by Raion (Top 30)"></h4>
				</div>
				<div class="col-md-2.5" id="raion1">
					<h4 data-i18n="Luhanska Oblast"></h4>
					<h4 data-i18n="Filter by Raion (Top 30)"></h4>
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

				<!--
				<div class="col-md-4">
					<h4>Filter by Donor (Top 20)</h4>
					<div id="donor" class="col-md-12"></div>
				</div>
				-->
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
					<div id="activity"></div>
				</div>
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-6">
							<h4 data-i18n="Filter by Agency (Top 20)"></h4>
							<div id="agency"></div>
						</div>
						<div class="col-md-6">
							<h4 data-i18n="Filter by Implementing Partner (Top 20)"></h4>
							<div id="partner"></div>
						</div>

						<div class="col-md-6">
							<h4 data-i18n="Number of activities reported by month"></h4>
							<h5 data-i18n="Data as of"></h5>
							<div id="tpo1" class="row"></div>
						</div>
						<div class="col-md-6">
							<h4 data-i18n="Number of activities reported by month"></h4>
							<h5 data-i18n="Start data activity"></h5>
							<div id="tpo" class="row"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6" align="left"></div>
				<div id="count-info1" class="col-md-5" align="right">
					<h4><span class="filter-count"></span><span class="total-count"></span></h4>
				</div>
				<div class="col-md-1" align="right">
					<a class="reset btn btn-primary btn-sm" id="reset" href="javascript:dc.filterAll();dc.redrawAll();" data-i18n="Reset All"></a>
				</div>
			</div>
			<!--
			<div class="row">
				<div class="col-md-4">
					<h4>Number of households Beneficiaries by Oblast</h4>
					<div id="benefhh" class="row"></div>
				</div>
				<div class="col-md-4">
					<h4>Number of individuals Beneficiaries by Oblast</h4>
					<div id="benefind" class="row"></div>
				</div>
			</div>
			-->
			<div class="row">
				<h8>_____________________________________________</h8> 
			</div>
			<div class="row">
				<h4 data-i18n="Developed by :"></h4>
			</div>
			<div class='row'>
				<img class="inlinelogo"  src="front/images/REACH_title10.png" alt="Reach Logo" /><br>
			</div>
		</div>

		<script type="text/javascript">
			var filters = {}
			filters[t('Oblast')] = []
			filters[t('Raion')] = []
			filters[t('Raion (Donetsk)')] = []
			filters[t('Raion (Luhansk)')] = []
			filters[t('Status')] = []
			filters[t('Cluster')] = []
			filters[t('Area')] = []
			filters[t('Activity')] = []
			filters[t('Agency')] = []
			filters[t('Partner')] = []
			filters[t('Month')] = []
			filters[t('Start')] = []

			filters[t('Small Suboff Map')] = []
			filters[t('Donor')] = []
			filters[t('Benefhh')] = []
			filters[t('Benefind')] = []

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

			var projection = d3.geo.mercator().center([39.4,48.9]).scale(6900)

			// var smallsuboffmapChart = dc.geoChoroplethChart("#map1")
			var distmapChart      = dc.geoChoroplethChart("#map1");

			var oblast_chart = dc.rowChart("#oblast")
				, cluster_chart = dc.rowChart("#cluster")
				, agency_chart = dc.rowChart("#agency")
				, partner_chart = dc.rowChart("#partner")
				// , donor_chart = dc.rowChart("#donor")
				, status_chart = dc.barChart("#status")
				, activity_chart = dc.rowChart("#activity")
				//, dataTable = dc.dataTable("#dc-table-graph")
				, area_chart = dc.barChart("#area")
				, raion_chart = dc.rowChart("#raion")
				, raion1_chart = dc.rowChart("#raion1")
				// , benefhh_chart = dc.rowChart("#benefhh")
				// , benefind_chart = dc.rowChart("#benefind")
				, startChart = dc.barChart("#tpo")
				, datofChart = dc.barChart("#tpo1")



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
				data: {loader: d3.csv, url: 'front/data/3W_shelter_Sample_Oblast.csv.php'}
				//, NGCABoundaries: {loader: d3.json, url: 'NGCA_boundaries.json'}
			}

			load(dataToLoad, function(result) {
				var data = result.data.data
					//, NGCABoundaries = result.NGCABoundaries.data


				// var debutFormat = d3.time.format("%d.%m.%Y");
				var dateFormat = d3.time.format('%m.%y');

				data.forEach(function (d) {
					d['dd'] = dateFormat.parse(d['START_ACTIVITY']);
					d['month'] = d3.time.month(d['dd']); // pre-calculate month for better performance
					//d.close = +d.close; // coerce to number
					//d.open = +d.open;

					if(d['Raion_Name'] == 'NA') {
						d['Raion_Name'] = 'N/A (' + d['Oblast_Name'] + ')'
					}
				})

				var cf = crossfilter(data)
				
				var raionDim = cf.dimension(function(d) { return d['Raion_Name'] })
					, raionGroup = raionDim.group().reduceCount()
					
				var raionNames = {}
				cf.dimension(function(d) { return d['Oblast_Name']} )
					.dispose()
					.group()
						.reduce(
							function(p, v, nf) { p[v['Raion_Name']] = true ; return p }
							, function(p, v, nf) { delete p[v['Raion_Name']] ; return p }
							, function() { return {} }
						)
						.all()
						.forEach(function(item) {
							raionNames[item.key] = Object.keys(item.value)
						})

				cf.oblast = cf.dimension(function(d){ return d.Oblast_Name; });
				// cf.raion2 = cf.dimension(function(d){ return d.Raion_Name; });
				cf.agency = cf.dimension(function(d){ return d.AGENCY; });
				cf.partner = cf.dimension(function(d){ return d.PARTNER; });
				cf.status = cf.dimension(function(d){ return d.STATUS_ACTIVITY; });
				//cf.benefhh = cf.dimension(function(d){ return d.BENEF_HH; });
				//cf.benefind = cf.dimension(function(d){ return d.BENEF_IND; });
				cf.cluster = cf.dimension(function(d){ return d.CLUSTER_ID; });
				// cf.donor = cf.dimension(function(d){ return d.DONOR; });
				cf.activity = cf.dimension(function(d){ return d.ACTIVITY; });
				cf.area = cf.dimension(function(d){ return d.area_type || t('Not specified') });
				cf.start = cf.dimension(function(d) { return (d.month) });
				cf.dataasof = cf.dimension(function(d) { return d.DATA_AS_OF == "janv.16" ? t('January 2016') : t('February 2016') });
				
				var oblast = cf.oblast.group()
					.reduceCount(function(d) { return d.Oblast_Name; });
				var cluster = cf.cluster.group()
					.reduceCount(function(d) { return d.CLUSTER_ID; }); // counts
				// var donor = cf.donor.group()
				// 	.reduceCount(function(d) { return d.DONOR; }); // counts
				var area = cf.area.group()
					.reduceCount(function(d) { return d.area_type; }); // counts
				var agency = cf.agency.group()
					.reduceCount(function(d) { return d.AGENCY; });
				
				var partner = cf.partner.group()
					.reduceCount(function(d) { return d.PARTNER; });
				var activity = cf.activity.group();	
				// var benefhh = cf.raion.group()
				// 	.reduceSum(function(d) { return d.BENEF_HH; });// sum
				// var benefind = cf.raion.group()
				// 	.reduceSum(function(d) { return d.BENEF_IND; });// sum
				var status = cf.status.group();
				// var raion = cf.raion.group()
				// 	.reduceCount(function(d) { return d.ADMIN2_ID; });
				// var raion1 = cf.raion1.group()
				// 	.reduceCount(function(d) { return d.ADMIN2_ID; });
				// var raion2 = cf.raion2.group()
				// 	.reduceCount(function(d) { return d.Raion_Name; });
				var startGroup = cf.start.group()
					.reduceCount(function(d) { return d.month; });
				var dataasofGroup = cf.dataasof.group()
					.reduceCount(function(d) { return d.DATA_AS_OF; });
				
				var all = cf.groupAll();








				window.saveData = function() {
					var header = [
						'DATA_AS_OF'
						, 'CLUSTER_ID'
						, 'AGENCY'
						, 'PARTNER'
						, 'PARTNER2'
						, 'DONOR'
						, 'ADMIN1_ID'
						, 'ADMIN2_ID'
						, 'area_type'
						, 'STATUS_ACTIVITY'
						, 'ACTIVITY'
						, 'START_ACTIVITY'
						, 'END_ACTIVITY'
						, 'BENEF_HH'
						, 'BENEF_IND'
						, 'Oblast_Name'
						, 'Raion_Name'
					]

					var data = cf.dataasof.top(Infinity)

					data = data.map(function(record) {
						return [
							record['DATA_AS_OF']
							, record['CLUSTER_ID']
							, record['AGENCY']
							, record['PARTNER']
							, record['PARTNER2']
							, record['DONOR']
							, record['ADMIN1_ID']
							, record['ADMIN2_ID']
							, record['area_type']
							, record['STATUS_ACTIVITY']
							, record['ACTIVITY']
							, record['START_ACTIVITY']
							, record['END_ACTIVITY']
							, record['BENEF_HH']
							, record['BENEF_IND']
							, record['Oblast_Name']
							, record['Raion_Name']
						]
					})

					data.unshift(header)

					var res = d3.tsv.formatRows(data)

					var blob = new Blob([res], {type: "text/tab-separated-values;charset=utf-8"})

					saveAs(blob, 'ukraine-3w-oblast-dataset-' + (new Date()).getTime() + '.tsv')
				}






				distmapChart.width(600)
					.height(620)
					.dimension(raionDim)
					.group(raionGroup)
					.colors(["#9ECAE1", "#6BAED6", "#4292C6", "#2171B5", "#084594", "#dddddd"])
					.colorDomain([0,5])
					.colorAccessor(function(d){
						if ((d>0) & (d<=50)) {return 0}
						else if ((d>50) & (d<=100)) {return 1}
						else if ((d>100) & (d<=200)) {return 2}
						else if ((d>200) & (d<=300)) {return 3}
						else if (d>300) {return 4}
						else { return 5}
					})
					.overlayGeoJson(adm2.features, "state", function (d) {
						return d.properties.HRNAME;
					})
					.projection(projection)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Raion')] = distmapChart.filters()
						updateFilters(newFilter)
					})

					
				oblast_chart.width(200).height(100)
					.margins({top: 10, left: 10, right: 40, bottom: 40})
					.dimension(cf.oblast)
					.group(oblast)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Oblast')] = oblast_chart.filters()
						updateFilters(newFilter)
					})
					.elasticX(true)
					.data(function(group) { return group.top(10) })
					.colors(['#026cb6'])
					.colorDomain([0,6])
					.colorAccessor(function(d, i){return i%6;})
					.xAxis().ticks(4)
				
				cluster_chart.width(300)
					.height(250)
					.margins({top: 10, right: 10, bottom: 40, left: 40})
					.dimension(cf.cluster)
					.group(cluster)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Cluster')] = cluster_chart.filters()
						updateFilters(newFilter)
					})
					.elasticX(true)
					.data(function(group) { return group.top(6) })
					.colors(['#026cb6'])
					.colorDomain([0,6])
					.colorAccessor(function(d, i){return i%6;})
					.xAxis().ticks(5)

				agency_chart.width(300)
					.height(450)
					.margins({top: 10, right: 10, bottom: 40, left: 40})
					.dimension(cf.agency)
					.group(agency)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Agency')] = agency_chart.filters()
						updateFilters(newFilter)
					})
					.elasticX(true)
					.data(function(group) { return group.top(20) })
					.colors(['#026cb6'])
					.colorDomain([0,1])
					.colorAccessor(function(d, i){return i%1;})
					.xAxis().ticks(4)

				partner_chart.width(300)
					.height(450)
					.margins({top: 10, right: 10, bottom: 40, left: 40})
					.dimension(cf.partner)
					.group(partner)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Partner')] = partner_chart.filters()
						updateFilters(newFilter)
					})
					.data(function(group) {
						return group.top(Infinity).filter(function(element) {return !!element.key}).slice(0,20)
					})
					.elasticX(true)
					.colors(['#026cb6'])
					.colorDomain([0,1])
					.colorAccessor(function(d, i){return i%1;})
					.xAxis().ticks(4)

				// donor_chart.width(300)
				// 	.height(350)
				// 	.margins({top: 10, right: 10, bottom: 40, left: 40})
				// 	.dimension(cf.donor)
				// 	.group(donor)
				// 	.on('filtered', function(chart, filter) {
				// 		updateFilters({'<?= _('Donor') ?>': donor_chart.filters()})
				// 	})
				// 	.elasticX(true)
				// 	.data(function(group) { return group.top(20) })
				// 	.colors(['#026cb6'])
				// 	.colorDomain([0,6])
				// 	.colorAccessor(function(d, i){return i%6;})
				// 	.xAxis().ticks(5)

				// benefhh_chart.width(300)
				// 	.height(500)
				// 	.margins({top: 10, right: 10, bottom: 40, left: 40})
				// 	.dimension(cf.oblast)
				// 	.group(benefhh)
				// 	.on('filtered', function(chart, filter) {
				// 		updateFilters({'<?= _('Benefhh') ?>': benefhh_chart.filters()})
				// 	})
				// 	.elasticX(true)
				// 	.data(function(group) { return group.top(28) })
				// 	.colors(['#026cb6'])
				// 	.colorDomain([0,2])
				// 	.colorAccessor(function(d, i){return i%2;})
				// 	.xAxis().ticks(4)

				// benefind_chart.width(300)
				// 	.height(500)
				// 	.margins({top: 10, right: 10, bottom: 40, left: 40})
				// 	.dimension(cf.oblast)
				// 	.group(benefind)
				// 	.on('filtered', function(chart, filter) {
				// 		updateFilters({'<?= _('Benefind') ?>': benefind_chart.filters()})
				// 	})
				// 	.elasticX(true)
				// 	.data(function(group) { return group.top(28) })
				// 	.colors(['#026cb6'])
				// 	.colorDomain([0,2])
				// 	.colorAccessor(function(d, i){return i%2;})
				// 	.xAxis().ticks(4)

				activity_chart.width(300).height(activity.size() * 20 + 55)
					.margins({top: 10, left: 10, right: 40, bottom: 40})
					.dimension(cf.activity)
					.group(activity)
					.data(function(group) { return group.top(Infinity) })
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Activity')] = activity_chart.filters()
						updateFilters(newFilter)
					})
					.elasticX(true)
					.colors(['#026cb6'])
					.colorDomain([0,8])
					.colorAccessor(function(d, i){return i%8;})
					.xAxis().ticks(4)

				raion_chart.width(200).height(600)
					.margins({top: 0, left: 10, right: 0, bottom: 40})
					.dimension(raionDim)
					.group(raionGroup)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Raion (Donetsk)')] = raion_chart.filters()
						updateFilters(newFilter)
					})
					.elasticX(true)
					.data(function(group) {
						return group.top(Infinity).filter(function(d) {
							return raionNames['Donetska'].indexOf(d.key) != -1
						}).splice(0, 30)
					})
					.colors(['#026cb6'])
					.colorDomain([0,8])
					.colorAccessor(function(d, i){return i%8;})
					/*
					.colors(['#871214','#b4181b','#e01f22','#e54143','#ea6264','#f08f90','#f6bcbd'])
					.colorDomain([7,0])
					.colorAccessor(function(d){
						if (d<=50) {return 0}
						else if ((d>50) & (d<=100)) {return 1}
						else if ((d>100) & (d<=150)) {return 2}
						else if ((d>150) & (d<=200)) {return 3}
						else if ((d>200) & (d<=250)) {return 4}
						else if ((d>250) & (d<=250)) {return 5}
						else if ((d>300) & (d<=350)) {return 6}
						else {return 7}
					})
					*/
					.xAxis().ticks(5);


				raion1_chart.width(200).height(600)
					.margins({top: 0, left: 10, right: 0, bottom: 40})
					.dimension(raionDim)
					.group(raionGroup)
					.elasticX(true)
					.data(function(group) {
						return group.top(Infinity).filter(function(d) {
							return raionNames['Luhanska'].indexOf(d.key) != -1
						}).splice(0, 30)
					})
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Raion (Luhansk)')] = raion1_chart.filters()
						updateFilters(newFilter)
					})
					.colors(['#026cb6'])
					.colorDomain([0,8])
					.colorAccessor(function(d, i){return i%8;})
					/*
					.colors(['#871214','#b4181b','#e01f22','#e54143','#ea6264','#f08f90','#f6bcbd'])
					.colorDomain([7,0])
					.colorAccessor(function(d){
						if (d<=50) {return 0}
						else if ((d>50) & (d<=100)) {return 1}
						else if ((d>100) & (d<=150)) {return 2}
						else if ((d>150) & (d<=200)) {return 3}
						else if ((d>200) & (d<=250)) {return 4}
						else if ((d>250) & (d<=250)) {return 5}
						else if ((d>300) & (d<=350)) {return 6}
						else {return 7}
					})
					*/
					.xAxis().ticks(5)
				
				status_chart.width(300)
					.height(250)
					.margins({top: 10, right: 10, bottom: 30, left: 40})
					.dimension(cf.status)
					.group(status)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Status')] = status_chart.filters()
						updateFilters(newFilter)
					})
					.transitionDuration(1000)
					.centerBar(false)
					.gap(40) // 65 = norm
					.colors("#026cb6")
					.x(d3.scale.ordinal().domain(["Completed", "Ongoing", "Planned", "Suspended"]))
					.xUnits(dc.units.ordinal)
					.elasticY(true)
					.xAxisLabel(t('Status'))
					.yAxisLabel(t('# of activities by status'))
					.xAxis().tickFormat()


				area_chart.width(300)
					.height(250)
					.margins({top: 10, right: 10, bottom: 30, left: 40})
					.dimension(cf.area)
					.group(area)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Area')] = area_chart.filters()
						updateFilters(newFilter)
					})
					.transitionDuration(1000)
					.centerBar(false)
					.gap(40) // 65 = norm
					.colors("#026cb6")
					.x(d3.scale.ordinal().domain(["GCA", "NGCA", t('Not specified')]))
					.xUnits(dc.units.ordinal)
					.elasticY(true)
					.xAxisLabel(t('Area type'))
					.yAxisLabel(t('# of activities by area'))
					.xAxis().tickFormat();
				
				startChart.width(350)
					.height(220)
					.margins({top: 30, right: 10, bottom: 40, left: 40})
					.dimension(cf.start)
					.group(startGroup)
					.on('filtered', function(event, filter) {
						var res = filter
						if(res && res.length > 0) {
							var res = [dateFormat(res[0]) + " &ndash; " + dateFormat(res[1])]
						}
						var newFilter = {}
						newFilter[t('Start')] = res
						updateFilters(newFilter)
					})
					.transitionDuration(500)
					.centerBar(true)
					.colors("#026cb6")
					.gap(65) // 65 = norm
					// .filter([3, 5])
					.x(d3.time.scale().domain([new Date(2013, 12, 1), new Date(2016, 6, 1)]))
					.elasticY(true)
					.xAxisLabel(t('Date'))
					.yAxisLabel(t('# of activities by date'))
					.xAxis().ticks(4);
					//.xAxis().tickFormat();
				
				datofChart.width(350)
					.height(220)
					.margins({top: 30, right: 10, bottom: 40, left: 40})
					.dimension(cf.dataasof)
					.group(dataasofGroup)
					.on('filtered', function(chart, filter) {
						var newFilter = {}
						newFilter[t('Month')] = datofChart.filters()
						updateFilters(newFilter)
					})
					.transitionDuration(500)
					.centerBar(false)
					.colors("#026cb6")
					.gap(50) // 65 = norm
					// .filter([3, 5])
					.x(d3.scale.ordinal().domain([t('January 2016'), t('February 2016')]))
					.xUnits(dc.units.ordinal)
					.elasticY(true)
					.xAxisLabel(t('Month'))
					.yAxisLabel(t('# of activities by date'))
					.xAxis().tickFormat();

				dc.dataCount("#count-info")
					.dimension(cf)
					.group(all);

				dc.renderAll();

				var g = d3.selectAll("#raion").select("svg").append("g");
				g.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 150).attr("y", 595).text(t('# of activities by Raion'))

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
					var legend = d3.select("#map1 svg").append("g");


					var legendDataset = [
						{x: 10, y: 140, type: "title", text: t('Number of activities')}
						, {x: 10, y: 160, type: "label", width: 30, height: 14, spacing: 5, fill: "#9ECAE1", text:"0-50"}
						, {x: 10, y: 180, type: "label", width: 30, height: 14, spacing: 5, fill: "#6BAED6", text:"51-100"}
						, {x: 10, y: 200, type: "label", width: 30, height: 14, spacing: 5, fill: "#4292C6", text:"101-200"}
						, {x: 10, y: 220, type: "label", width: 30, height: 14, spacing: 5, fill: "#2171B5", text:"201-300"}
						, {x: 10, y: 240, type: "label", width: 30, height: 14, spacing: 5, fill: "#084594", text:"> 301"}
						, {x: 10, y: 270, type: "divider", width: 110, height: 1, fill: "#cccccc"}
						, {x: 10.5, y: 280.5, type: "label", width: 29, height: 13, spacing: 5, fill: "url('#diagonalHatch')", text:"NGCA", stroke: "#e01f22"}
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
					var mapSVG = d3.select("#map1 svg")
						, container = mapSVG.append("g")

					var color = "#e01f22"

					mapSVG
						.append("defs")
							.append("pattern")
								.attr("id", "diagonalHatch")
								.attr("patternUnits", "userSpaceOnUse")
								.attr("width", 10)
								.attr("height", 10)
								.append("path")
									.attr("d", "M -1,1 l 2,-2    M 0,10 l 10,-10    M 9,11 l 2,-2")
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
					var mapSVG = d3.select("#map1 svg")
						, container = mapSVG.append("g")

					container.selectAll("path")
						.data(adm1.features)
						.enter().append("path")
							.attr("d", d3.geo.path().projection(projection))
							.attr("fill", "none")
							.attr("stroke", "#000000")
							.attr("stroke-width", "2px")
							.attr("pointer-events", "none")
				}

				{
					var all = dc.chartRegistry.list()
					var chartPrimary = distmapChart
						, chartsSecondary = [raion_chart, raion1_chart]

					chartsSecondary.forEach(function(chart) {
						chart.onClick = function(datum) {
							var selected = chart.keyAccessor()(datum)

							chartPrimary.filter(selected)

							chart.redrawGroup()
						}

						chart.hasFilter = function(filter) {
							var filters = chartPrimary.filters()
							if(!filter) { return filters.length > 0 }
							return filters.indexOf(filter) != -1
						}
					})
				}

			})
		</script>
	</body>
</html>
