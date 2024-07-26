<?php

if(isset($_GET['id'])){
	if(empty($_GET['id']) && empty($_GET['rfid'])){
		echo '<script type = "text/javascript">alert("Check you details."); window.location.replace("void_transaction");</script>';
	}else{
		$stmt = $conn->prepare("UPDATE transactions set active = 3 where md5(concat(receipt, 'void')) = ? and md5(concat(rfid_no, 'void')) = ? and active = 2 ");
		$stmt->bind_param("ss", $_GET['id'], $_GET['rfid']);
		if($stmt->execute() === TRUE){
			if ($stmt->affected_rows) {
				$stmt = $conn->prepare("SELECT * FROM transactions where md5(concat(receipt, 'void')) = ? and md5(concat(rfid_no, 'void')) = ? and active = 3 ");
				$stmt->bind_param("ss", $_GET['id'], $_GET['rfid']);
				if($stmt->execute() === TRUE){
					$result = $stmt->get_result();
					while ($row = $result->fetch_assoc()) {
						$stmt2 = $conn->prepare("UPDATE products set stock = stock +  ? where product_id = ?");
						$stmt2->bind_param("ss", $row['qty'], $row['product_id']);
						$stmt2->execute();
						if ($row['isload']) {
							$stmt2 = $conn->prepare("UPDATE members set balance = balance + ? where rfid_no = ?");
							$stmt2->bind_param("ss", $row['amount'], $row['rfid_no']);
							$stmt2->execute();
						}
					}
				}
				echo '<script type = "text/javascript">alert("Void Successfull."); window.close();</script>';
			}else {
				echo '<script type = "text/javascript">alert("Void failed."); window.close();</script>';
			}
		}
	}
}