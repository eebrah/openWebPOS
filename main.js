/*
    @licstart  The following is the entire license notice for the JavaScript code in this page.

    Copyright (C) 2013  Ibrahim K. Ngeno

    The JavaScript code in this page is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License (GNU GPL) as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

    The code is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details. As additional permission under GNU GPL version 3 section 7, you may distribute non-source (e.g., minimized or compacted) forms of that code without the copy of the GNU GPL normally required by section 4, provided you include this license notice and a URL through which recipients can access the Corresponding Source.

	@licend  The above is the entire license notice for the JavaScript code in this page.

*/

function genRandomString( seed = "01234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ", length = 5 ) {

	var returnValue = new String( "" );

	for( var i = 0; i < length; i++ ) {

		returnValue += seed[ Math.floor( Math.random() * ( 34 + 1 ) ) ]

	}

	return returnValue;

}

Base = function( uniqueID ) {

	this.getUniqueID = function() { return this.uniqueID; };

	if( uniqueID == undefined ) { this.uniqueID = genRandomString(); }
	else { this.uniqueID = uniqueID }

}

Product = function( uniqueID, name, price, description ) {

	Base.apply( this );

	this.price = price;
	this.description = description;
	this.name = name;

	this.getPrice = function() { return this.price; };
	this.getName = function() { return this.name; };
	this.getDescription = function() { return this.description; };

};

Product.prototype = new Base();

Entry = function( uniqueID, productID, quantity ) {

	Base.apply( this );

	this.productID = productID;
	this.quantity = quantity;

	this.getProduct = function() { return this.product };
	this.getQuantity = function() { return this.quantity };

	this.createTableRow = function( index ) {

		returnValue = '';

		return returnValue;

	}

}

Entry.prototype = new Base();
