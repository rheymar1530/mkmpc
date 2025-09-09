
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	function initialize_pie_chart($elem,$init_data,$type,$legend_position){
		var pieRevenueCanvas=$('#'+$elem).get(0).getContext('2d');
		
		var pieOptions={
			tooltips : {
				callbacks: { 
					label: function(tooltipItem, data) { 
						return data.labels[tooltipItem.index]+': '+data.datasets[0].data[tooltipItem.index].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") 
						}
					}, 
				},
				aspectRatio: 2,
				layout : {
					padding : 20
				},
				plugins: {
					legend: {
						display : true,
						position: $legend_position,
						maintainAspectRatio: true,
						labels: {
							generateLabels: (chart) => {
								const datasets = chart.data.datasets;
								return datasets[0].data.map((data, i) => ({
									text: `${chart.data.labels[i]} : ₱${number_format(data,2)}`,
									fillStyle: datasets[0].backgroundColor[i],
								}))
							}
						}
					},
					datalabels: {
						color : '#ffffff',
						anchor: 'end',
						align: 'center',
						clamp: true,
						display: true, 
						fontStyle : 'bolder',
						position : 'outside',
						backgroundColor: function(context) {
							// return context.dataset.backgroundColor;
							return '#00000080';
						},
						formatter: function (value, ctx) {
							const dt = ctx.chart.data.datasets[0].data;
							function totalSum(total,datapoint){
								return total + datapoint;
							}
							let total_dt = dt.reduce(totalSum,0);
							return roundoff((value/total_dt)*100,2)+'%';
							return total_perc;
						},
					},
				},
			}

			//doughnut plugin
			const doughnutLabelsLine = {
				id : 'doughnutLabelsLine',
				afterDraw(chart,args,options){
					const {ctx, chartArea : {top,bottom,left,right,width,height} } = chart;

					chart.data.datasets.forEach((dataset,i) => {
						
						chart.getDatasetMeta(i).data.forEach((datapoints,index) => {
							const {x,y} = datapoints.tooltipPosition();

							// console.log({x,y,width,height})

							// ctx.fillStyle = dataset.backgroundColor[index];
							// ctx.fill();
							// ctx.fillRect(x,y,10,10);
							var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
								// console.log( previousValue + currentValue);
								return parseFloat(previousValue) + parseFloat(currentValue);
							});
							

							var currentValue = dataset.data[index];

							var percentage = roundoff((currentValue/total)*100);

							if(currentValue > 0){

								const halfwidth = width/2;
								const halfheight = height/2;

								const xLine = x >= halfwidth ? x+0 : x-0;
								const yLine = y >= halfheight ? y+56 : y-50;
								const extraLine = x >= halfwidth ? 0 : 0;

								ctx.beginPath();
								// ctx.moveTo(x,y-42);
								// ctx.lineTo(xLine,yLine);
								// ctx.lineTo(xLine+extraLine,yLine);

								ctx.strokeStyle = dataset.backgroundColor[index];
							// ctx.strokeStyle = 'white';

								ctx.stroke();

								const textWidth = ctx.measureText(chart.data.labels[index]);

								const textXPosition = x >= halfwidth ? 'left' : 'right';
								const extraPX = x >= halfwidth ? 0 : -0;
								ctx.font = '12px Arial';
								ctx.textAlign = textXPosition;
								ctx.textBaseLine = 'middle';
								ctx.fillStyle = 'blue';



								ctx.fillRect(xLine+extraLine+extraPX,yLine, textWidth, 12);
								

								
							// ctx.fillText(chart.data.labels[index], xLine+extraLine+extraPX,yLine);
								ctx.fillText(percentage+"\n%", xLine+extraLine+extraPX,yLine);
							}

						})
					})

				}

			};

			var pieRevenue=new Chart(pieRevenueCanvas,
				{type:$type,
				data:$init_data,

				options:pieOptions,
				// doughnutLabelsLine
				plugins : [ChartDataLabels]
			});
		}
		function initialize_line_chart($elem,$init_data,$stack,$show_label){

			var ticksStyle = {
				fontColor: '#495057',
				fontStyle: 'bold'
			}
			var ticks = $.extend({
				beginAtZero: true,
				callback: function (value) {

					if ( Math.abs(value) >= 1000 && Math.abs(value) <= 999999) {
						value /= 1000
						value += 'k'
					}else if(Math.abs(value) >= 1000000){
						value /= 1000000
						value += 'M'            		
					}
					return '₱' + value
				}
			}, ticksStyle);

			var LineCanvas=$('#'+$elem).get(0).getContext('2d');
			var LineOptions={
				plugins: {
					legend: {
						display : $show_label,
						position: 'top',
						maintainAspectRatio: true	
					},
				},
				hover:{
					mode:"x",
					intersect:"intersect"
				},
				tooltips : {
					callbacks: { 
						label: function(tooltipItem, data) { 
							return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
						}, 

					},
					scales: {
						x: {
							stacked: $stack,
							ticks : ticksStyle
						},
						y: {
							stacked: $stack,
							ticks : ticks
						}
					}

				}

				var mode = 'index';
				var intersect = true;
				var Line=new Chart(LineCanvas,
					{type:'line',
					data:$init_data,
					options : LineOptions
				});
			}
			function initialize_bar_chart($type,$elem,$init_data,$stack,$show_label,$aspect_ratio){

				var ticksStyle = {
					fontColor: '#495057',
					fontStyle: 'bold'
				}
				var ticks = $.extend({
					beginAtZero: true,
					callback: function (value) {
						if ( Math.abs(value) >= 1000 && Math.abs(value) <= 999999) {
							value /= 1000
							value += 'k'
						}else if(Math.abs(value) >= 1000000){
							value /= 1000000
							value += 'M'            		
						}
						return '₱' + value
					}
				}, ticksStyle);


				if($type=='bar'){
					$y_tick = ticks;
					$x_tick = ticksStyle;

					$index_axis = 'x';
				}else{
					$x_tick = ticks;
					$y_tick = ticksStyle;
					$index_axis = 'y';

				}

				var mode = 'index'
				var intersect = true;

				var $salesChart = $('#'+$elem);
				var salesChart = new Chart($salesChart, {
					type: 'bar',
					data: $init_data,


					options: {

						maintainAspectRatio: $aspect_ratio,
						indexAxis: $index_axis,

						hover: {
							mode: mode,
							intersect: intersect
						},
						tooltips : {
							mode: 'nearest',
							callbacks: { 
								label: function(tooltipItem, data) { 
									if($type == "horizontalBar"){
										return tooltipItem.xLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
									}else{
										return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
									}
								}
							},
						},
						plugins: {
							legend: {
								display : $show_label,
								position: 'top',
							},
						},
						scales: {
							x: {
								stacked: $stack,
								ticks : $x_tick
							},
							y: {
								stacked: $stack,
								ticks : $y_tick,
								beginAtZero: true
							}
						}
					}
				})
			}
			function goFullScreen(obj,div){

		   	// fas fa-compress-arrows-alt

				var obj = $(obj);
		   		//INIT FULL SCREEN
				if($(obj).find('i.fas').hasClass('fa-expand')){
				// max-height: calc(100vh - 50px)

					$('.div_top,.div_table').css({'max-height' : 'calc(100vh - 50px)'});
					var elem = document.getElementById(div);

					if(elem.requestFullscreen){
						elem.requestFullscreen();
					}
					else if(elem.mozRequestFullScreen){
						elem.mozRequestFullScreen();
					}
					else if(elem.webkitRequestFullscreen){
						elem.webkitRequestFullscreen();
					}
					else if(elem.msRequestFullscreen){
						elem.msRequestFullscreen();
					}

					$(obj).find('i.fas').removeClass('fa-expand').addClass('fa-compress-arrows-alt')	;  			
				}else{
					exitFullScreen();

				}


			}
			function exitFullScreen(){

				if(document.exitFullscreen){
					document.exitFullscreen();
				}
				else if(document.mozCancelFullScreen){
					document.mozCancelFullScreen();
				}
				else if(document.webkitExitFullscreen){
					document.webkitExitFullscreen();
				}
				else if(document.msExitFullscreen){
					document.msExitFullscreen();
				}

				$('.btn-full-screen').find('i.fas').removeClass('fa-compress-arrows-alt').addClass('fa-expand')	;

			}

			if (document.addEventListener){

				document.addEventListener('fullscreenchange', exitHandler, false);
				document.addEventListener('mozfullscreenchange', exitHandler, false);
				document.addEventListener('MSFullscreenChange', exitHandler, false);
				document.addEventListener('webkitfullscreenchange', exitHandler, false);
			}
			function exitHandler()
			{
				if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement)
				{
					$('.btn-full-screen').find('i.fas').removeClass('fa-compress-arrows-alt').addClass('fa-expand')	;
					let max_height = '<?php echo e($DASHBOARD_TYPE); ?>' == '1'?'calc(100vh + 200px)':'calc(100vh - 250px)';
					$('.div_top').css({'max-height' : max_height});
					$('.div_table').css({'max-height' : ''});
					
					// div_table
					// $('.div_top').css({'max-height' : 'calc(100vh)'});
				}else{

				}
			}


			function change_top(obj){
				$.ajax({
					type        :      'GET',
					data        :      {'type'   : '<?php echo e($DASHBOARD_TYPE); ?>',
					'month' : '<?php echo e($selected_month); ?>',
					'year' : '<?php echo e($selected_year); ?>',
					'limit' : $(obj).val()},
					url         :      '/admin_dashboard/top/parse',
					beforeSend  :      function(){
						show_loader();
					},
					success     :      function(response){
						console.log({response});
						hide_loader();
						var out = ``;

						$.each(response.OUTPUT,function(i,items){
							$.each(items,function(cc,item){
								out += `<tr class="text-md font-weight-bold lbl_color vertical-center">`;
								if(cc == 0){
									out += `<td class="text-center border-right" rowspan="${items.length}">${(i+1)}</td>`;
								}
								

								out+=	`<td>${item.member}</td>
								<td class="text-right">${number_format(item.amount,2)}</td>
								</tr>`;
							});

						});

						$('#'+response.BODY).html(out);
					},error: function(xhr, status, error){
						hide_loader();
						var errorMessage = xhr.status + ': ' + xhr.statusText;
						Swal.fire({
							position: 'center',
							icon: 'warning',
							title: "Error-"+errorMessage,
							showConfirmButton: false,
							showCancelButton: true,
							cancelButtonText : "Close"
						})
					}
				})
			}



		</script>
		<?php $__env->stopPush(); ?>


<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/admin_dashboard/chart_config.blade.php ENDPATH**/ ?>