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

		<script src="front/js/bootstrap.js"></script>

		<script src="front/js/d3.min.js"></script>

		<script src="front/js/crossfilter.js"></script>
		<script src="front/js/dc.js"></script>

		<script src="front/js/Blob.js"></script>
		<script src="front/js/FileSaver.js"></script>

		<link rel="stylesheet" href="front/css/leaflet.css" />
		<script src="front/js/leaflet.js"></script>


		<script src="front/ukr_adm1b.js"></script>
		<script src="front/NGCA_boundaries.js"></script>
		<script src="front/data/Settlements_points_clear.js"></script>
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

			.leaflet-container {
				cursor: default;
			}

			.leaflet-clickable {
				cursor: default;
			}
			.map-clickable:hover, .map-selected {
				stroke: #58f;
			}
			.leaflet-marker-pane .leaflet-clickable, .map-clickable {
				cursor: pointer;
			}

			hr {
				border-color: #888;
			}
	
			#benefAge.dc-chart rect.bar,
			#benefGender.dc-chart rect.bar {
				cursor: default;
			}
			#benefAge.dc-chart rect.bar:hover,
			#benefGender.dc-chart rect.bar:hover {
				fill-opacity: 1;
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
					<a class="navbar-brand" href="index_education.php" data-i18n="UKRAINE - 3W Dashboard - Education"></a>
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
								<li><a href="index_education.php">EDUCATION</a></li>
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
				<div class="col-md-8">
					<div id="map"></div>
				</div>

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
					<h4 data-i18n="Filter by Agency (Top 20)"></h4>
					<div id="agency"></div>
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
					<div id="activity"></div>
				</div>
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-6">
							<h4 data-i18n="Number of activities reported by month"></h4>
							<h5 data-i18n="Data as of"></h5>
							<div id="dataasof"></div>
						</div>
						<div class="col-md-6">
							<h4 data-i18n="Number of activities reported by month"></h4>
							<h5 data-i18n="Start data activity"></h5>
							<div id="date"></div>
						</div>
					</div>
				</div>
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
				<div class="col-md-6">
					<h4 data-i18n="Number of beneficiaries by Age"></h4>
					<div id="benefAge"></div>
				</div>
				<div class="col-md-6">
					<h4 data-i18n="Number of beneficiaries by Gender"></h4>
					<div id="benefGender"></div>
				</div>
			</div>

			<div class="row">
				<hr />
				<h4 data-i18n="Developed by :"></h4>
				<img  src="front/images/REACH_title10.png" alt="Reach Logo" width="250px"/><br>
			</div>
		</div>
	<script type="text/javascript">

		var dateFormat = d3.time.format('%Y-%m-%d')
			, datePrint = d3.time.format('%b %y')


		var oblastByKOATUU = {}
		adm1.features.map(function(item) {
			oblastByKOATUU[item.properties.KOATUU] = item
		})


		var filters = {}
		filters[t('Data As Of')] = []
		filters[t('Activity')] = []
		filters[t('Area')] = []
		filters[t('Oblast')] = []
		filters[t('Start')] = []
		filters[t('Status')] = []
		filters[t('Agency')] = []


		var $appliedFilters = $('#appliedFilters')
		var updateFilters = function(filter) {
			$.extend(filters, filter)
			
			var $fragment = $(document.createDocumentFragment())

			for(var name in filters) {
				if(filters[name] && filters[name].length > 0) {
					var prints = filters[name].map(function(value) {

						if(value.filterType && value.filterType == 'RangedFilter') {
							value = [
								datePrint(value[0])
								, datePrint(value[1])
							]
						}
						return value
					})
					$fragment.append('<li>' + name + ": " + prints.join(', ') + '</li>')
				}
			}

			$appliedFilters.html($fragment)
		}

		var projection = d3.geo.mercator().center([36,48]).scale(1900)



		var mapChart
			, oblast_chart = dc.rowChart("#oblast")
			, status_chart = dc.barChart("#status")
			, activity_chart = dc.rowChart("#activity")
			, area_chart = dc.barChart("#area")
			, benef_age_chart = dc.barChart("#benefAge")
			, benef_gender_chart = dc.barChart("#benefGender")
			, startChart = dc.barChart("#date")
			, dataasofChart = dc.barChart("#dataasof")
			, agency_chart = dc.rowChart("#agency")
			


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
			data: {loader: d3.csv, url: 'front/data/Education_UKR_3W_MASTER_2016-04-13_with_coords.csv.php'}
		}

		load(dataToLoad, function(result, error) {
			var data = result.data.data

			var index = 0
			data.forEach(function (d) {
				d.dateAsOf = dateFormat.parse(d['Day, Month, Year'])
				d.dateAsOfOpt = new Date(d.dateAsOf.getFullYear(), d.dateAsOf.getMonth())
				
				d.dd = dateFormat.parse(d['Activity start date']);
				d.ddOpt = new Date(d.dd.getFullYear(), d.dd.getMonth())
				// d.month = d.dd ? d3.time.month(d.dd) : null; // pre-calculate month for better performance

				d['Total Beneficiaries'] = parseInt(d['Total Beneficiaries']) || 0
				d['Total ADULT Beneficiaries'] = parseInt(d['Total ADULT Beneficiaries']) || 0
				d['Total CHILDREN Beneficiaries'] = parseInt(d['Total CHILDREN Beneficiaries']) || 0
				
				d['Male'] = parseInt(d['Male']) || 0;
				d['Female'] = parseInt(d['Female']) || 0;
				d['Boys'] = parseInt(d['Boys']) || 0;
				d['Girls'] = parseInt(d['Girls']) || 0;
			});
			
			var cf = crossfilter(data)

			cf.oblast = cf.dimension(function(d) { return oblastByKOATUU[d['PCODE1']] ? oblastByKOATUU[d['PCODE1']].properties['NAME_LAT'] : d[' Oblast'] })
			cf.oblastKOATUU = cf.dimension(function(d) { return d['PCODE1'] })

			cf.agency = cf.dimension(function(d){ return d['Organization'] })
			cf.start = cf.dimension(function(d) { return d.ddOpt })

			cf.dataasof = cf.dimension(function(d) { return d.dateAsOfOpt })
			
			
			cf.status = cf.dimension(function(d){ return d['Status'] })

			cf.agency = cf.dimension(function(d){ return d['Organization'] })
			
			cf.activity = cf.dimension(function(d){ return d['Activity'] })

			cf.area = cf.dimension(function(d){ return d['Control area'] || t('Not specified') });
			
			var oblast = cf.oblast.group().reduceCount()
				, oblastKOATUU = cf.oblastKOATUU.group().reduceCount()
			
			var area = cf.area.group()
				.reduceCount(function(d) { return d['Control area'] }) // counts 
			
			var activity = cf.activity.group();

			var status = cf.status.group();

			var agency = cf.agency.group()
				.reduceCount(function(d) { return d['Organization'] })

			var startGroup = cf.start.group().reduceCount()
			
			var dataasofGroup = cf.dataasof.group().reduceCount()

			var age = cf.oblast.group().reduce(function(p, v) {
					p['total'] += v['Total Beneficiaries']
					p['adult'] += v['Total ADULT Beneficiaries']
					p['children'] += v['Total CHILDREN Beneficiaries']
					p['notspec'] += v['Total Beneficiaries'] - v['Total ADULT Beneficiaries'] - v['Total CHILDREN Beneficiaries']
					return p
				}
				, function(p, v) {
					p['total'] -= v['Total Beneficiaries']
					p['adult'] -= v['Total ADULT Beneficiaries']
					p['children'] -= v['Total CHILDREN Beneficiaries']
					p['notspec'] -= v['Total Beneficiaries'] - v['Total ADULT Beneficiaries'] - v['Total CHILDREN Beneficiaries']
					return p
				}
				, function() { return {
					'total': 0
					, 'adult': 0
					, 'children': 0
					, 'notspec': 0
				}}
			)
			.order(function(p) { return p['total'] })
			var gender = cf.oblast.group().reduce(function(p, v) {
					p['total'] += v['Total Beneficiaries']
					p['male'] += v['Male'] + v['Boys']
					p['female'] += v['Female'] + v['Girls']
					p['notspec'] += v['Total Beneficiaries'] - (v['Male'] + v['Boys']) - (v['Female'] + v['Girls'])
					return p
				}
				, function(p, v) {
					p['total'] -= v['Total Beneficiaries']
					p['male'] -= v['Male'] + v['Boys']
					p['female'] -= v['Female'] + v['Girls']
					p['notspec'] -= v['Total Beneficiaries'] - (v['Male'] + v['Boys']) - (v['Female'] + v['Girls'])
					return p
				}
				, function() { return {
					'total': 0
					, 'male': 0
					, 'female': 0
					, 'notspec': 0
				}}
			)
			.order(function(p) { return p['total'] })


			var all = cf.groupAll();



			window.saveData = function() {
				var header = [
					'Day, Month, Year'
					, 'Organization'
					, 'Acronym'
					, ' Oblast'
					, 'PCODE1'
					, 'Raion'
					, 'PCODE2'
					, 'Urban-type settlement'
					, 'PCODE3'
					, '[Admin4]'
					, 'PCODE4'
					, 'Name of school / learning center'
					, 'Type of school/ learning center'
					, 'Type of education'
					, 'Control area'
					, 'Activity'
					, 'Status'
					, 'Activity start date'
					, 'Activity completion date'
					, 'Total Beneficiaries'
					, 'Total ADULT Beneficiaries'
					, 'Female'
					, 'Male'
					, 'Total CHILDREN Beneficiaries'
					, 'Girls'
					, 'Boys'
					, 'Number of ECCD Units'
					, 'Number of Schools/Kindergartens'
					, 'Comments'
				]

				var data = cf.dataasof.top(Infinity)

				data = data.map(function(record) {
					return [
						record['Day, Month, Year']
						, record['Organization']
						, record['Acronym']
						, record[' Oblast']
						, record['PCODE1']
						, record['Raion']
						, record['PCODE2']
						, record['Urban-type settlement']
						, record['PCODE3']
						, record['[Admin4]']
						, record['PCODE4']
						, record['Name of school / learning center']
						, record['Type of school/ learning center']
						, record['Type of education']
						, record['Control area']
						, record['Activity']
						, record['Status']
						, record['Activity start date']
						, record['Activity completion date']
						, record['Total Beneficiaries']
						, record['Total ADULT Beneficiaries']
						, record['Female']
						, record['Male']
						, record['Total CHILDREN Beneficiaries']
						, record['Girls']
						, record['Boys']
						, record['Number of ECCD Units']
						, record['Number of Schools/Kindergartens']
						, record['Comments']
					]
				})

				data.unshift(header)

				var res = d3.csv.formatRows(data)

				var blob = new Blob([res], {type: "text/csv;charset=utf-8"})

				saveAs(blob, 'ukraine-3w-education-dataset-' + (new Date()).getTime() + '.csv')
			}



			benef_age_chart.width(550)
				.clipPadding(50)
				.height(300)
				.margins({top: 40, right: -1, bottom: 20, left: -1})
				.dimension(cf.oblast)
				.group(age, 'adult', function(d) { return d.value['adult'] })
				.stack(age, 'children', function(d) { return d.value['children'] })
				.stack(age, 'notspec', function(d) { return d.value['notspec'] })
				.x(d3.scale.ordinal().domain(  age.top(Infinity).map(function(d) { return d.key })  ))
				.xUnits(dc.units.ordinal)
				.elasticY(true)
				.elasticX(false)
				.renderLabel(true)
				.title(function(d) {
					var human
					switch(this.layer) {
						case 'adult': human = t('Adult'); break;
						case 'children': human = t('Children'); break;
						case 'notspec': human = t('Not Specified'); break;
					}
					return human + ': ' + d.value[this.layer]
				})
				.ordinalColors(['#026CB6', '#6BAEF1', '#dddddd'])
				.legend(dc.legend().x(460).y(40).legendText(function(data) {
					switch(data.name) {
						case 'adult': return t('Adult');
						case 'children': return t('Children');
						case 'notspec': return t('Not Specified');
					}
				}))
				.onClick = function() {}
			benef_age_chart.yAxis().ticks(0)
			benef_age_chart.xAxis().tickFormat(function(label) { return label.substr(0,10) })

			benef_gender_chart.width(550)
				.clipPadding(50)
				.height(300)
				.margins({top: 40, right: -1, bottom: 20, left: -1})
				.dimension(cf.oblast)
				.group(gender, 'male', function(d) { return d.value['male'] })
				.stack(gender, 'female', function(d) { return d.value['female'] })
				.stack(gender, 'notspec', function(d) { return d.value['notspec'] })
				.x(d3.scale.ordinal().domain(   gender.top(Infinity).map(function(d) { return d.key })    ))
				.xUnits(dc.units.ordinal)
				.elasticY(true)
				.elasticX(false)
				.renderLabel(true)
				.title(function(d) {
					var human
					switch(this.layer) {
						case 'male': human = t('Male'); break;
						case 'female': human = t('Female'); break;
						case 'notspec': human = t('Not Specified'); break;
					}
					return human + ': ' + d.value[this.layer]
				})
				.ordinalColors(['#026CB6', '#6BAEF1', '#dddddd'])
				.legend(dc.legend().x(460).y(40).legendText(function(data) {
					switch(data.name) {
						case 'male': return t('Male');
						case 'female': return t('Female');
						case 'notspec': return t('Not Specified');
					}
				}))
				.onClick = function() {}
			benef_gender_chart.yAxis().ticks(0)
			benef_gender_chart.xAxis().tickFormat(function(label) { return label.substr(0,10) })




			status_chart.width(300)
				.height(450)
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



			agency_chart.width(300)
				.height(450)
				.margins({top: 10, right: 10, bottom: 40, left: 40})
				.dimension(cf.agency)
				.group(agency)
				.elasticX(true)
				.data(function(group) { return group.top(20) })
				.colors(['#026CB6'])
				.colorDomain([0,1])
				.colorAccessor(function(d, i){return i%1;})
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Agency')] = agency_chart.filters()
					updateFilters(filters)
				})
				.xAxis().ticks(4)


			area_chart.width(300)
				.height(450)
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
				.xAxis().ticks(4)



			startChart.width(350)
				.height(220)
				.margins({top: 30, right: 0, bottom: 40, left: 25})
				.dimension(cf.start)
				.group(startGroup)
				.centerBar(true)
				.colors("#026CB6")
				.gap(5)
				.x(d3.time.scale())
				.xUnits(d3.time.months, 1)
				.elasticY(true)
				.elasticX(true)
				.xAxisLabel(t('Date'))
				.yAxisLabel(t('# of activities by date'))
				.on('filtered', function(event, filter) {
					var res = filter

					if(res && res.length > 0) {
						var res = [dateFormat(res[0]) + "&ndash;" + dateFormat(res[1])]
					}
					var filters = {}
					filters[t('Start')] = startChart.filters()
					updateFilters(filters)
				})
				.xAxisPadding(15)
				.xAxis().ticks(d3.time.month, 2).tickFormat(function(label) { return datePrint(label) })


			dataasofChart.width(350)
				.height(220)
				.margins({top: 30, right: 0, bottom: 40, left: 25})
				.dimension(cf.dataasof)
				.group(dataasofGroup)
				.gap(5)
				.colors("#026CB6")
				.x(d3.time.scale())
				.xUnits(d3.time.months, 1)
				.centerBar(true)
				.elasticY(true)
				.xAxisLabel(t('Month'))
				.yAxisLabel(t('# of activities by date'))
				.on('filtered', function(chart, filter) {
					var filters = {}
					filters[t('Data As Of')] = dataasofChart.filters()
					updateFilters(filters)
				})
				.elasticX(true)
				.xAxisPadding(15)
				.xAxis().ticks(d3.time.month, 3).tickFormat(function(label){ return datePrint(label) })

			
			dc.dataCount("#count-info")
				.dimension(cf)
				.group(all);

			{

				var countGeom = function(oblast) {
					if(!oblast.bbox) {
						var minLatitude = Number.MAX_SAFE_INTEGER, maxLatitude = Number.MIN_SAFE_INTEGER
							, minLongitude = Number.MAX_SAFE_INTEGER, maxLongitude = Number.MIN_SAFE_INTEGER
						
						for(var i = 0; i < oblast.geometry.coordinates[0][0].length; ++i) {
							var coord = oblast.geometry.coordinates[0][0][i]

							if(coord[1] < minLatitude) { minLatitude = coord[1]}
							else if(coord[1] > maxLatitude) { maxLatitude = coord[1]}
						
							if(coord[0] < minLongitude) { minLongitude = coord[0] }
							else if(coord[0] > maxLongitude) { maxLongitude = coord[0] }
						}

						oblast.bbox = [[minLatitude, minLongitude], [maxLatitude, maxLongitude]]
					}

					if(!oblast._center) {
						oblast._center = [(oblast.bbox[0][0] + oblast.bbox[1][0]) / 2.0, (oblast.bbox[0][1] + oblast.bbox[1][1]) / 2.0]
					}
				
					return { bbox: oblast.bbox, center: oblast._center }
				}

				var $map = d3.select('#map')
				$map.style({'width': '750px', 'height': '450px'})
				var map = L.map($map.node(), {
					center: [48.36, 32.04]
					, zoom: 5
					, minZoom: 5
					, maxZoom: 9
					, maxBounds: [
						[54.521081495443596, 48.515625]
						, [41.37680856570233, 15.556640624999998]
					]
				})



				L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
					attribution: '<a href="http://osm.org/copyright">OpenStreetMap</a>'
				}).addTo(map);


				var resetZoom = {
					enabled: false
					, disable: function() {
						if(this.enabled) {
							map.setView(this._prevCenter, this._prevZoom)
							this.enabled = false
						}
					}
					, enable: function(center) {
						this._prevCenter = map.getCenter()
						this._prevZoom = map.getZoom()

						map.setView(center, 7)
						this.enabled = true
					}
				}

				var colorScale = d3.scale.quantile()
					.domain([0, 1, 10, 20, 30, 40, 50])
					.range(['#dddddd', '#C6DBEF', '#9ECAE1', '#6BAEF1', '#4292C6', '#2171B5', '#084594'])


				var oblastsLayer, markersLayer, drowMarkers = false
					, featureDispatcher = d3.dispatch('click')

				oblastsLayer = L.geoJson(adm1, {
					style: {'fillOpacity': .9, 'color': '#fff', 'weight': 2, 'opacity': 1}
					, onEachFeature: function(feature, layer) {
						layer.on('click', function(event) {
							featureDispatcher.click(event, feature, layer)
						})
					}
				})

				oblast_chart.on('pretransition', function(chart) {

					var data = {}
					oblast.all().forEach(function(item) { data[item.key] = item.value })

					oblastsLayer.eachLayer(function(featureLayer) {
						var KOATUU = featureLayer.feature.properties['KOATUU']
						var value = data[featureLayer.feature.properties['NAME_LAT']]

						featureLayer.setStyle({
							'fillColor': ['0100000000', '8500000000'].indexOf(KOATUU) == -1 ? colorScale(value || 0) : 'rgba(0,0,0,0)'
						})


						featureLayer.eachLayer(function(layer) {
							var $container = d3.select(layer._container)

							// set/reset title
							var $title = $container.select('title')
							if($title.empty()) $title = $container.append('title')
							$title.text(featureLayer.feature.properties['NAME_LAT'] + ': ' + (value || 0))

							var $path = d3.select(layer._path)
							if(value !== undefined) {
								$path.classed({'map-clickable': true})
								layer.on('mouseover', layer.bringToFront)
							} else {
								$path.classed({'map-clickable': false})
							}
						})
					})

					if(markersLayer) { map.removeLayer(markersLayer) }
					if(drowMarkers) {
						var records = cf.oblast.top(Infinity)
						var markers = {}
						for(var i = 0, record; record = records[i], i < records.length; ++i) {
							if(!record['Urban-type settlement'] || !record['Latitude'] || !record['Longitude']) { continue }

							var marker = markers[record['PCODE3']] = markers[record['PCODE3']] || L.marker([record['Latitude'], record['Longitude']], {count: 0, clickable: true})

							++marker.options.count

							marker.bindPopup(record['Urban-type settlement'] + ': ' + marker.options.count)
						}
						markers = Object.keys(markers).map(function(key) { return markers[key] })
						markersLayer = L.layerGroup(markers)
						map.addLayer(markersLayer)
					}
				})


				var deselect = function() {
					d3.selectAll('.map-selected', map.svg).classed({'map-selected': false})
					if(markersLayer) map.removeLayer(markersLayer)
					drowMarkers = false
					dc.redrawAll()
				}

				var selectOblast = function(name) {
					// reset previous
					deselect()
					
					for(var i = 0, layers = oblastsLayer.getLayers(), featureLayer; featureLayer = layers[i], i < layers.length; ++i) {
						if(featureLayer.feature.properties['NAME_LAT'] != name) { continue }

						resetZoom.enable(countGeom(featureLayer.feature).center)
						
						// set current
						featureLayer.eachLayer(function(layer) {
							d3.select(layer._path).classed({'map-selected': true})
							layer.bringToFront()
						})

						drowMarkers = true
					}

					dc.redrawAll()
				}

				var selectOblasts = function(names) {
					deselect()

					for(var i = 0, featureLayer, layers = oblastsLayer.getLayers(); featureLayer = layers[i], i < layers.length; ++i) {
						if(names.indexOf(featureLayer.feature.properties['NAME_LAT']) == -1) { continue }
						// set current
						featureLayer.eachLayer(function(layer) {
							d3.select(layer._path).classed({'map-selected': true})
							layer.bringToFront()
						})
					}

					dc.redrawAll()
				}

				map.addLayer(oblastsLayer, {})


				featureDispatcher.on('click', function(event, feature, layer) {
					var active = false

					for(var curLayer = layer, subLayers = layer.getLayers(), i = -1; i < subLayers.length; ++i, curLayer = subLayers[i]) {
						if(curLayer._path && d3.select(curLayer._path).classed('map-clickable')) {
							active = true
							break
						}
					}

					if(active) {
						oblast_chart.filter(feature.properties['NAME_LAT'])
					}
				})

				oblast_chart.on('filtered', function(chart, filter){
					var filters = chart.filters()
					if(filters.length == 1) {
						selectOblast(filters[0])
					} else if ( filters.length > 1 ) {
						resetZoom.disable()
						selectOblasts(filters)
					} else {
						resetZoom.disable()
						deselect()
					}


					// dont forget to update filters
					var filters = {}
					filters[t('Oblast')] = oblast_chart.filters()
					updateFilters(filters)

				})
			}



			dc.renderAll();


			var g = d3.selectAll("#oblast").select("svg").append("g");
			g.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 150).attr("y", 495).text(t('# of activities by Oblast'))

			var g4 = d3.selectAll("#activity").select("svg").append("g");
			g4.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 140).attr("y", activity_chart.height() - 10).text(t('# of activities by type'))

			var g5 = d3.selectAll("#benefhh").select("svg").append("g");
			g5.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 160).attr("y", 490).text(t('# of hh beneficiairies'))
			
			var g6 = d3.selectAll("#benefind").select("svg").append("g");
			g6.append("text").attr("class", "x-axis-label").attr("text-anchor", "middle").attr("x", 160).attr("y", 490).text(t('# of individuals beneficiaries'))

		})
	</script>
</body>
</html>
