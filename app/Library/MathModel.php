<?php namespace App\library;

class MathModel {
	
	public static function getAproxDistance ($setPoint, $windSpeed) {
		$windSpeed = $windSpeed == 0 ? 1 : $windSpeed;
		$getSz = function ($x) { // Based on the table
	        /// A CASE
	        $category = 'A';
	        if ($x < 0.10) {
	          $a = 122.800; $b = 0.94470;
	        } else if ($x >= 0.10 && $x < 0.15) {
	          $a = 158.080; $b = 1.05420;
	        } else if ($x >= 0.16 && $x < 0.20) {
	          $a = 170.220; $b = 1.09320;
	        } else if ($x >= 0.21 && $x < 0.25) {
	          $a = 179.520; $b = 1.12620;
	        } else if ($x >= 0.26 && $x < 0.30) {
	          $a = 217.410; $b = 1.26440;
	        } else if ($x >= 0.31 && $x < 0.40) {
	          $a = 258.890; $b = 1.40940;
	        } else if ($x >= 0.41 && $x < 0.50) {
	          $a = 346.750; $b = 1.72830;
	        } else if ($x >= 0.51 && $x < 3.11) {
	          $a = 453.850; $b = 2.11660;
	        }

	         /// B CASE
	        else if ($x < 0.20) {
	          $category = 'B';
	          $a = 90.673; $b = 0.93198;
	        } else if ($x >= 0.21 && $x < 0.40) {
	          $category = 'B';
	          $a = 98.483; $b = 0.98332;
	        } 

	         /// D CASE 
	        else if ($x <= 0.30) {
	          $category = 'D';
	          $a = 34.459; $b = 0.86974;
	        } else if ($x >= 0.31 && $x < 1.00) {
	          $category = 'D';
	          $a = 32.093; $b = 0.81066;
	        } else if ($x >= 1.01 && $x < 3.00) {
	          $category = 'D';
	          $a = 32.093; $b = 0.64403;
	        } else if ($x >= 3.01 && $x < 10.00) {
	          $category = 'D';
	          $a = 33.504; $b = 0.60486;
	         } else if ($x >= 10.01 && $x < 30.00) {
	          $category = 'D';
	          $a = 36.650; $b = 0.56589;
	        } 
	        
	         /// E CASE
	        else if ( $x <= 0.10) {
	          $category = 'E';
	          $a = 24.260; $b = 0.83660;
	        } else if ( $x >= 0.10 && $x < 0.30) {
	          $category = 'E';
	          $a = 23.331; $b = 0.81956;
	        } else if ( $x >= 0.31 && $x < 1.00) {
	          $category = 'E';
	          $a = 21.628; $b = 0.75660;
	        } else if ( $x >= 1.01 && $x < 2.00) {
	          $category = 'E';
	          $a = 21.628; $b = 0.63077;
	        } else if ( $x >= 2.01 && $x < 4.00) {
	          $category = 'E';
	          $a = 22.534; $b = 0.57154;
	        } else if ( $x >= 4.01 && $x < 10.00) {
	          $category = 'E';
	          $a = 24.703; $b = 0.50527;
	         } else if ( $x >= 10.01 && $x < 20.00) {
	          $category = 'E';
	          $a = 26.970; $b = 0.46713;
	        } else if ( $x >= 20.01 && $x < 40.00) {
	          $category = 'E';
	          $a = 35.420; $b = 0.37615;
	        } 

	         /// F CASE
			else if ( $x <= 0.20) {
			$category = 'F';
			$a = 15.209; $b = 0.81558;
			} else if ( $x >= 0.21 && $x < 0.70) {
			$category = 'F';
			$a = 14.457; $b = 0.78407;
			} else if ( $x >= 0.71 && $x < 1.00) {
			$category = 'F';
			$a = 13.953; $b = 0.68465;
			} else if ( $x >= 1.01 && $x < 2.00) {
			$category = 'F';
			$a = 13.953; $b = 0.63227;
			} else if ( $x >= 2.01 && $x < 3.00) {
			$category = 'F';
			$a = 14.823; $b = 0.54503;
			} else if ( $x >= 3.01 && $x < 7.00) {
			$category = 'F';
			$a = 16.187; $b = 0.46490;
			} else if ( $x >= 7.01 && $x < 15.00) {
			$category = 'F';
			$a = 17.836; $b = 0.41507;
			} else if ( $x >= 15.01 && $x < 30.00) {
			$category = 'F';
			$a = 22.651; $b = 0.32681;
			} else if ( $x >= 30.01 && $x < 60.00) {
			$category = 'F';
			$a = 27.074; $b = 0.27436;
			} else if ( $x > 60.00) {
			$category = 'F';
			$a = 34.219; $b = 0.21716;
			}
	          
	        return  (object)['category' => $category, 'value' => $a *pow( $x, $b)];
	    };

    	$getTheta = function ($x) use ($getSz) {
	        $cat = $getSz($x)->category;
	        if ($cat == 'A') {
	          $c = 24.1670; $d = 2.5334;
	        } else if ($cat == 'B') {
	          $c = 18.3330; $d =  1.8096;
	        } else if ($cat == 'C') {
	          $c = 12.5000; $d =  1.0857;
	        } else if ($cat == 'D') {
	          $c = 8.3330; $d =  0.72382;
	        } else if ($cat == 'E') {
	          $c = 6.2500; $d =  0.54287;
	        } else if ($cat == 'F') {
	          $c = 4.1667; $d =  0.36191;
	        } 

	        return $theta = 0.017453293 * ($c - ($d * log($x)));
	        // return [$x, $c, $d, $theta, $getSz($x)];      
      	};

      $getSy = function ($x) use ($getTheta) {
        return 465.11628 * $x * tan($getTheta($x));
      };

      $C = function ($v, $Sy, $Sz)  {
        $A = $v->Q / (2 * M_PI * $v->u * $Sy * $Sz);
        $exp1 = ((-1) * pow(($v->z - $v->h), 2)) / (2 * pow($Sz, 2));
        $exp2 = ((-1) * pow(($v->z + $v->h), 2)) / (2 * pow($Sz, 2));
        $exp3 = ((-1) * pow($v->y, 2)) / (2 * pow($Sy, 2));
        return $A * (exp($exp1) + exp($exp2)) * exp($exp3);
      };

      $ugToPPm = function ($ug) {
        return ($ug * 24.04) / 44.01;
      };

      // $setPoint = 5000;
      $aprox = 0 ;
      $x = 0.00001;
      
      $params = (object) [
        'Q' => 40, // grams/sec
        'h' => 10, // m 
        'u' => $windSpeed, // m/s -> wind velocity
        'z' => 30,
        'y' => 0
      ];


      for ($i = 3; $i >= 0.001; $i -= 0.001) { 
       $aprox = $ugToPPm($C($params, $getSy($i), $getSz($i)->value) * 1000000);
       $x = $i;
       $span = 10;
       if ($aprox < ($setPoint + $span) && $aprox > ($setPoint - $span))  {
        break;
       }
      }

      // print_r($x);
      // die();
      return $x;
	}
}