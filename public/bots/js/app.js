	

	$(function(){

		var mode = 1; // 0 -> random, 1 -> seq
		             
		if (mode == 0) {
			console.warn('#########   MODO RANDOM TOTAL #########');
		} else {
			console.warn('#########   MODO SECUENCIAL #########');
		}
		var timer = null, 
		    interval = 4000,
		    value = 0;

		$("#counter-area").hide()

		$("#start").click(function() {
			console.log('------START BOT ATACK-------')

			$("#start").attr('disabled','disabled')
			
			$("#counter-area").show()
			 
			// var sensorsInfo = {};
			// $.ajax({
			//     type: 'GET',
			//     url: 'http://cotwo-api.com/v1/sensors',
			//     dataType: 'json',
			//     cache: false,
			//     async: false,
			//     success: function (data) {
			//     	sensorsInfo = data;
			//     }
			// });

    	console.info("######### GETTING SENSORS DATA FROM API ######")
    	
    	// var wind_info = [];
	    // var sensors_info = [];

	    if (timer !== null) return;
	    	var value = -1;
			timer = setInterval(function () {
				++value;
			$("#counter").text(value);
			// console.info(value);  
			console.info(measure.packages[value]);  
			// console.log(JSON.stringify(measure.packages));
			// // seq
			// if (mode === 1 && undefined !== measure.packages[value]) {
			// 	var pack = measure.packages[value];
			// 	console.warn(pack); 
			// 	$.each(pack.wind, function (windIndex, windInfo){
			// 		wind_info.push({
			// 			"identifier": windInfo.identifier,
			// 			"velocity": windInfo.velocity,
			// 			"unit":  windInfo.unit,
			// 			"direction":  windInfo.direction,
			// 		});	
			// 	});
			// 	$.each(pack.co2, function (co2Index, co2Info){
			// 		sensors_info.push({
			// 			"identifier": co2Info.identifier,
			// 			"ppm": co2Info.ppm
			// 		});
			// 	});
			// } else {
			// 	$.each(sensorsInfo, function (index, value){
			// 		if (value.type_id === 1) {
			// 			wind_info.push({
			// 				"identifier": value.identifier,
			// 				"velocity": getRandomArbitrary(20, 45),
			// 				"unit": "km/s",
			// 				"direction": ["n", "s", "e", "o", "ne", "no", "se", 'so'][Math.floor(Math.random() * ["n", "s", "e", "o", "ne", "no", "se", 'so'].length)]
			// 			});	
			// 		} else {
			// 			sensors_info.push({
			// 				"identifier": value.identifier,
			// 				"ppm": getRandomArbitrary(0, 100)
			// 			});
			// 		}
			// 	});
			// }
			
					
			// var totalInfo = wind_info.concat(sensors_info);
			// //Display in table
			// displayData( value, totalInfo);
			// // Post data to measurement table
			console.info('--- SAVING TO MEASUREMENT ---');

			$.ajax({
		    type: 'GET',
		    url: 'http://localhost/v1/measurements',
		    data: measure.packages[value],
		    dataType: 'json',
		    cache: false,
		    async: true,
		    success: function (data) {
			  }
			});		

			// console.log(JSON.stringify(totalInfo));

			// // Reset Info
			// wind_info = [];
			// sensors_info = [];
			// totalInfo = [];

   		}, interval); 
		});

		$("#stop").click(function() {
			console.log('------END BOT ATACK-------')
			$("#start").removeAttr('disabled');
			$("#population").empty()

			$("#counter").text('0')
			$("#counter-area").hide()

		    clearInterval(timer);
		    timer = null
		});
	});

	// function displayData( value, allValues) {
	// 	// console.warn(wind_info);return;
	// 	$("#population").empty();
	// 	$("#population").append("<tr>"+
	// 		"<td>" + allValues[0].velocity.toFixed(2) + " | " + allValues[0].direction + "</td>"+
	// 		"<td>" + allValues[1].velocity.toFixed(2) + " | " + allValues[1].direction + "</td>"+
	// 		"<td>" + allValues[2].velocity.toFixed(2) + " | " + allValues[2].direction + "</td>"+
	// 		"<td>" + allValues[3].velocity.toFixed(2) + " | " + allValues[3].direction + "</td>"+
	// 		"<td>" + allValues[4].ppm.toFixed(2) + "</td>"+
	// 		"<td>" + allValues[5].ppm.toFixed(2) + "</td>"+
	// 		"<td>" + allValues[6].ppm.toFixed(2) + "</td>"+
	// 		"<td>" + allValues[7].ppm.toFixed(2) + "</td>"+
	// 		"</tr>"
	// 	);
	// }

