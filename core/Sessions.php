<?php

class Sessions
{
	
	public static function session_exist($name)
	{
		if(isset($_SESSION[$name]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function session($name)
	{
		return $_SESSION[$name];
	}
	
	public static function set_session($dataArray)
	{
		foreach($dataArray as $key => $value)
		{
			$_SESSION[$key] = $value;
		}
	}
	
	public static function unset_session($data)
	{
		if(is_array($data))
		{
			foreach($data as $key)
			{
				if(self::session_exist($key))
				{
					unset($_SESSION[$key]);
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			unset($_SESSION[$data]);	
		}
		
		return true;
	}
	
	public static function uagent_no_version()
	{
		$uagent = $_SERVER['HTTP_USER_AGENT'];
		$regex = '/\/[a-zA-Z0-9.]+/';
		$new_uagent = preg_replace($regex,'', $uagent);
		
		return $new_uagent;
	}
	
	public function addToCart()
	{
		$pid = $_POST['id'];
		$quantity = $_POST['quantity'];
		
		$i = 0;
		$hasproduct = false;
		
		
		// If the cart session variable is not set or cart array is empty
		if (!self::session_exist('cart') || count(self::session("cart")) < 1) 
		{ 
			/// Set the product in the cart
			$_SESSION["cart"] = array(0 => array("productID" => $pid, "quantity" => $quantity));
		} 
		else 
		{
			foreach (self::session("cart") as $each_item) 
			{ 
				$i++;
				while (list($key, $value) = each($each_item)) 
				{
					if ($key == "productID" && $value == $pid) 
					{
						// Increase quantity if product is already in cart
						array_splice($_SESSION["cart"], $i-1, 1, array(array("productID" => $pid, "quantity" => $each_item['quantity'] + $quantity)));
						$hasproduct = true;
					}
				}
			}
			if ($hasproduct == false) 
			{
			   array_push($_SESSION["cart"], array("productID" => $pid, "quantity" => $quantity));
			}
		}
		
		echo "added";
	}
	
	public function removeFromCart()
	{
		$key = $_POST['index'];
		
		if (count($_SESSION['cart']) <= 1) 
		{
			self::unset_session('cart');
			
			self::unset_session('shippingfee');
			self::unset_session('coupon');
			self::unset_session('voucher');
			self::unset_session('finalTotal');
			self::unset_session('orderNo');
			self::unset_session('orderID');
			self::unset_session('payType');
			self::unset_session('tax');
		} 
		else 
		{
			unset($_SESSION['cart']["$key"]);
			sort($_SESSION['cart']);
		}
		
		echo "removed";
	}
	
	public function emptyCart()
	{
		self::unset_session('cart');
		self::unset_session('shippingfee');
		self::unset_session('coupon');
		self::unset_session('voucher');
		self::unset_session('finalTotal');
		self::unset_session('payType');
		self::unset_session('orderNo');
		self::unset_session('orderID');
		self::unset_session('tax');
	}
	
	public function cartQuantity()
	{
		$pid = $_POST['id'];
		$quantity = $_POST['quantity'];
		$quantity = preg_replace('#[^0-9]#i', '', $quantity); // filter everything but numbers
		
		if ($quantity >= 30) 
		{ 
			$quantity = 30; 
		}
		
		if ($quantity < 1) 
		{ 
			$quantity = 1; 
		}
		
		if ($quantity == "") 
		{ 
			$quantity = 1; 
		}
		
		$i = 0;
		
		foreach (self::session('cart') as $each_item) 
		{ 
			$i++;
			while (list($key, $value) = each($each_item)) 
			{
				if ($key == "productID" && $value == $pid) 
				{
					// That item is in cart already so let's adjust its quantity using array_splice()
					array_splice($_SESSION['cart'], $i-1, 1, array(array("productID" => $pid, "quantity" => $quantity)));
				}
			}
		}
		
		echo "updated";
	}
}
?>
