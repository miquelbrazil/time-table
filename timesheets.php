<?php

	date_default_timezone_set('America/New_York');
	
	
	
	function set_calendar () {
		
		$weeks = array();
		
		$pto = array();
	
		$w = 1;
		
		while ( $w <= 52 ) {
			
			/* set the counter to cycle through 5 days of a work week */
			$d = 1;
			
			while ( $d <= 5 ) {
			
				$week_num = str_pad( $w , 2 , '0' , STR_PAD_LEFT );
				
				$week_day = strftime( '%A' , strtotime( '2012-W' . $week_num . '-' . $d ) );
				
				$week_date = strftime( '%m/%d/%Y' , strtotime( '2012-W' . $week_num . '-' . $d ) );
				
				
				/* set the data for the day of the work week */
				$weeks[ $week_num ][ $week_day ] = array( 'date' => $week_date , 'hours' => 8 , 'type' => 'WORK' );
				
				
				if ( array_key_exists( $week_date , $pto ) ) {
					
					// use the type of time taken
					
				}
				
				
				$d++;
				
			}
			
			$w++;      // increment week counter
			
		}
		
		// print_r( $weeks );
		
		return $weeks;
		
	}
	
	
	function calculate_project_schedule ( $project , &$project_week ) {

		/* reset total work week hours for next cycle */
		$work_week_total_hours = 0;
		
	
		/*foreach ( $calendar_week as $calendar_day ) {
			
			$work_week_total_hours = $work_week_total_hours + $calendar_day['hours'];
			
		}*/
		
		//$project_week = $calendar_week;
		
		
		/* calculate the the project hours for a work week based on the project allocation */
		$project_week_hours = round ( ( ( 40 * $project['alloc'] ) / .25 ) * .25 , 1 );
		
		//echo $project_week_hours . "\n\n";
		
		echo "Project Name: " . $project['name'] . "\n\n";
		
		while ( $project_week_hours > 0 ) {
	
			foreach ( $project_week as $project_day_name => &$project_day ) {
				
				if ( $project_day['hours'] <= 1 ) {
				
					$hours_worked = $project_day['hours'];
					
				} elseif ( $project_week_hours <= $project_day['hours'] ) {
				
					$hours_worked = mt_rand( 0 , ( $project_week_hours * 10 ) ) / 10;
					
				} else {
				
					$hours_worked = mt_rand( 0 , ( $project_day['hours'] * 10 ) ) / 10;
					
				}
				
				
				
				/* round hours worked to the nearest .25 interval */
				
				if ( $project_day['hours'] <= 1 ) {
					
					echo "Total calculated hours worked on $project_day_name (no rounding because remaining time is less than or equal to 1) = $hours_worked\n";
					$hours_worked = $project_week_hours;
					
				} else {
				
					/* display hours worked for the day BEFORE rounding to .25 */
					echo "Total calculated hours worked on $project_day_name (BEFORE rounding) = $hours_worked\n";
					
					/* round hours worked for the day to .25 */
					$hours_worked = round( ( $hours_worked / .25 ) ) * .25;
					
					/* display hours worked for the day AFTER rounding to .25 */
					echo "Total calculated hours worked on $project_day_name (AFTER rounding) = $hours_worked\n";
					
				}
				
				
				if ( $hours_worked > $project_day['hours'] ) {
				
					if ( $project_day['hours'] > 0 ) {
					
						$project_day['hours'] = round( $project_day['hours'] , 2 );
						
						echo "There are " . $project_day['hours'] . " working hours available for $project_day_name.\n";
						
						echo "The total $hours_worked hour(s) is greater than the total available time to work. But I was able to deduct the remaining " . $project_day['hours'] . " hour(s) for $project_day_name.\n\n";
						
						$project_week_hours = $project_week_hours - $project_day['hours'];
						
						$project_week_schedule[ $project_day['date'] ][] = $project_day['hours'];
					
						$project_day['hours'] = $project_day['hours'] - $project_day['hours'];
						
						echo "There are " . $project_day['hours'] . " remaining working hours available for " . $project_day['hours'] . "\n\n";
						
					} elseif ( !$project_day['hours'] && $project_week_hours < .01 ) {
					
						echo "The $hours_worked hour(s) worked is greater than the total available time to work. No hours were available to deduct from $project_day_name.\n\n";
						$project_week_hours = 0;
						
					}
					
				} else {
				
					/* display the total number of hours available to work on the given day */
					echo "There are " . $project_day['hours'] . " remaining working hours available for $project_day_name.\n";
					
					/* display the total hours calculated as work on the given day */
					echo "Based on this model, Tracy worked $hours_worked hours on $project_day_name.\n";
					
					/* calculate the total remaining hours for the given day after deducting total calculated work hours */
					$project_day['hours'] = $project_day['hours'] - $hours_worked;
					
					/* display the total number of remaining available work hours for the given day */
					echo "After deducting the calculated work hours, there are " . $project_day['hours'] . " remaining working hours available for $project_day_name.\n\n";
				
					/* calculate the total number of remaining project hours available for the work week */
					$project_week_hours = $project_week_hours - $hours_worked;
				
					echo "There are $project_week_hours remaining work hour(s) for this project this week.\n\n";
					
					/* add the calculated hours worked for day to the project week array */
					$project_week_schedule[ $project_day['date'] ][] = $hours_worked;
					
					if ( $project_week_hours < .1 ) {
						
						$project_week_hours = 0;
						
					}
					
				}
				
				if ( $project_week_hours <= 0 ) {
					
					break;
						
				}
					
			}
			
			//print_r( $calendar_week );
			
		}
		
		/* loop through the project week schedule and add the calculated worked hours together */
		foreach ( $project_week_schedule as &$p ) {
		
			$p = round( array_sum($p) , 2 );
			
		}
		
		return $project_week_schedule;
		
	}
	
	
	/* set up variable for employee */
	
	$employee = array (
		'name' => 'Tracy Jones',
		'projects' => array (
			array (
				'name' => '330j',
				'alloc' => .05
			),
			array (
				'name' => '305J',
				'alloc' => .05
			),
			array (
				'name' => '323j',
				'alloc' => .05
			),
			array (
				'name' => '301j',
				'alloc' => .025
			),
			array (
				'name' => '566k',
				'alloc' => .02
			),
			array (
				'name' => '661j',
				'alloc' => .025
			),
			array (
				'name' => '304j',
				'alloc' => .10
			),
			array (
				'name' => 'Admin',
				'alloc' => .68
			),
		)
	);
	
	
	$pto = array(
	
		'01/02/2012' => 'HOLIDAY',
		'01/16/2012' => 'HOLIDAY',
		'02/20/2012' => 'HOLIDAY',
		'03/14/2012' => 'PERSONAL',
		'05/09/2012' => 'PERSONAL',
		'05/28/2012' => 'HOLIDAY',
		'07/04/2012' => 'HOLIDAY',
		'08/03/2012' => 'HOLIDAY',
		'11/22/2012' => 'HOLIDAY',
		'11/23/2012' => 'HOLIDAY',
		'12/24/2012' => 'HOLIDAY',
		'12/25/2012' => 'HOLIDAY',
		'12/31/2012' => 'HOLIDAY',
		'08/12/2012' => 'VACATION',
		'08/13/2012' => 'VACATION',
		'08/14/2012' => 'VACATION',
		'08/15/2012' => 'VACATION',
		'08/16/2012' => 'VACATION',
		'08/17/2012' => 'VACATION',
		'08/18/2012' => 'VACATION',
		'08/19/2012' => 'VACATION',
		'08/20/2012' => 'VACATION',
		'10/15/2012' => 'SICK',
		'10/16/2012' => 'SICK',
	
	);
	
	/* display unedited employee data */
	//print_r($employee);
	
	
	$calendar_weeks = set_calendar();
	
	$w = 1;
	
	while ( $w <= count( $calendar_weeks ) ) {
	
		$week_num = str_pad( $w , 2 , '0' , STR_PAD_LEFT );
		
		$week_day = strftime( '%A' , strtotime( '2012-W' . $week_num . '-' . $d ) );
		
		$calendar_week = $calendar_weeks[ $week_num ];
		
		//print_r( $calendar_week );
	
		foreach ( $employee['projects'] as &$project ) {
	
			$project['schedule'][ $week_num ] = calculate_project_schedule( $project , $calendar_week );
			
			
			foreach ( $project['schedule'][ $week_num ] as $project_date => &$hours_worked ) {
			
				if ( array_key_exists( $project_date , $pto ) ) {
					
					$hours_worked = $pto[ $project_date ];
					
				}
				
			}
			
			
			
		}
		
		foreach ( $calendar_week as $calendar_day => $calendar_details ) {
			
			//print_r( $calendar_details );
			
			$data[] = array ( 
				$calendar_details['date'],
				$employee['projects'][0]['schedule'][ $week_num ][ $calendar_details['date'] ],
				$employee['projects'][1]['schedule'][ $week_num ][ $calendar_details['date'] ],
				$employee['projects'][2]['schedule'][ $week_num ][ $calendar_details['date'] ],
				$employee['projects'][3]['schedule'][ $week_num ][ $calendar_details['date'] ],
				$employee['projects'][4]['schedule'][ $week_num ][ $calendar_details['date'] ],
				$employee['projects'][5]['schedule'][ $week_num ][ $calendar_details['date'] ],
				$employee['projects'][6]['schedule'][ $week_num ][ $calendar_details['date'] ],
				$employee['projects'][7]['schedule'][ $week_num ][ $calendar_details['date'] ],
			);
			
		}
		
		$w++;
	}
	
	print_r($employee);
	
	print_r($data);
	
	$output = fopen( 'output.csv' , 'w' );
	
	$header = array (
		'Date',
		'Project 1: ' . $employee['projects'][0]['name'],
		'Project 2: ' . $employee['projects'][1]['name'],
		'Project 3: ' . $employee['projects'][2]['name'],
		'Project 4: ' . $employee['projects'][3]['name'],
		'Project 5: ' . $employee['projects'][4]['name'],
		'Project 6: ' . $employee['projects'][5]['name'],
		'Project 7: ' . $employee['projects'][6]['name'],
		'Project 8: ' . $employee['projects'][7]['name'],
	);
	
	fputcsv( $output , $header );
	
	foreach ( $data as $fields ) {
		
		fputcsv( $output , $fields );
		
	}
	
	fclose( $output );
	
?>