
/*
 * Ikantam AjaxCart
 * client-side part
 * @author isxam
 * 
 */

var Ikantam = window.Ikantam || {};

Ikantam.AjaxCart = {
		
	config	: {
		
	},

    linkers : [],
	
	blocks	: [],

    _session : 0,
	
	addBlock	: function(block){
		Ikantam.AjaxCart.blocks.push(block);
	},
	
	getBlock	: function(id){
		var blocks = Ikantam.AjaxCart.blocks;
		for(var i = 0; i < blocks.length; i++){
			if( blocks[i].id == id ){
				return blocks[i];
			}
		}
	},	
	
	query       : function(session, url, params){

        if(this.getSession() != session){
            return true;
        }

        Ikantam.AjaxCart.showPreloader();

		jQuery.ajax({
	        url: url,
            data : params,
	        success: function(data) {
			    Ikantam.AjaxCart.renderResponse(data);
                Ikantam.AjaxCart.init();
		    },
            complete : function(){
                Ikantam.AjaxCart.hidePreloader();
            }
		});

	},
	
	renderResponse	: function(data){
		
		if( undefined === data.blocks ) {
			throw new Error("Blocks not found");
		}
		
		for( var blockId in data.blocks ){
			
			var block = Ikantam.AjaxCart.getBlock(blockId);
			block.setContent(data.blocks[blockId]);
			
		}
		
	},
	
	
	init	: function(){

        var self = Ikantam.AjaxCart;
        self._session++;
        for( var i = 0; i < self.linkers.length; i++ ){
            self.linkers[i].init();
        }

    },

    addLinker   : function(linker){
        Ikantam.AjaxCart.linkers.push(linker);
    },

    registerEvent   : function(self, element, url, params){

        var session = this.getSession();
        (function(self, element, url, params, session){
            element.on("click", function(e){

                self.controller.query(session, url, params);
                return false;

            });
        })(self, element, url, params, session);

    },

    getSession  : function(){
        return this._session;
    },

    showPreloader   : function(){
        jQuery("#iajaxcart-preloader").show();
    },

    hidePreloader   : function(){
        jQuery("#iajaxcart-preloader").hide();
    }
	
	
		
};


/**
 * Render block
 * @param id block identifier
 * @param selector css selector
 * @constructor
 */
Ikantam.AjaxCart.Block = function(id, selector){
	
	this.id = id;
	this.selector = selector;
	
};

Ikantam.AjaxCart.Block.prototype = {

    /**
     * Set content in block with selector
     * @param content html string
     */
	setContent	: function (content){
		jQuery(this.selector).replaceWith(content);
	}
	
};


Ikantam.AjaxCart.CartLinker = function(controller, config){

    this.controller = controller;
    this.config = config;

};


Ikantam.AjaxCart.CartLinker.prototype = {

    init   : function(){

        this.validator = new Ikantam.AjaxCart.LinkValidator([
            'checkout/cart/add',
            'checkout/cart/delete'
        ],{ 'checkout/cart' : 'iajaxcart/index' }
        );

        this._unbindSetLocation();

        this._initSetLocation();
        this._initRemoveLink();

    },

    _unbindSetLocation  : function(){

        var elems = jQuery( this.config.selector.setLocation );

        for( var i = 0; i < elems.length; i++){

            var elem = jQuery(elems[i]);
            var defaultOnclick;
            if( defaultOnclick = elem.attr("defaultOnclick")){
                elem.attr("onclick", defaultOnclick);
                elem.removeAttr("defaultOnclick");
            }
        }

        return this;
    },

    _initSetLocation    : function(){

        var elems = jQuery( this.config.selector.setLocation );

        for( var i = 0; i < elems.length; i++){

            var data;
            if( data = this._isSetLocationValid(elems[i]) ){

                var elem = jQuery(elems[i]);
                var defaultOnclick = elem.attr("onclick");
                elem.attr("defaultOnclick", defaultOnclick);
                elem.removeAttr("onclick");

                this.controller.registerEvent(this, elem, data, {});

            }
        }


    },

    _isSetLocationValid : function(elem){

        elem = jQuery(elem);
        var onclick = elem.attr("onclick");
        if(!onclick || onclick.indexOf("setLocation") === -1) return false;

        var regUrl = /setLocation\('([\w'-:]*)'\)/;
        var found = onclick.match(regUrl);
        if( found == null ){
            return false;
        }
        var url = found[1];
        if(this.validator.isValid(url)){
            return this.validator.prepareUrl(url);
        }

        return false;
    },

    _initRemoveLink   : function(){

        var links = jQuery(this.config.selector.removeLink);

        for( var i = 0; i < links.length; i++){
            var link = jQuery(links[i]);
            var data;
            if( data = this._isRemoveLinkValid(link)){
                this.controller.registerEvent(this, link, data, {});
            }
        }

    },

    _isRemoveLinkValid  : function(elem){

        var url = elem.attr("href");
        if(this.validator.isValid(url)){
            return this.validator.prepareUrl(url);
        }
        return false;

    }

};






/**
 * Linker for add to cart from product page ( or configure page)
 * @param controller
 * @param config
 * @constructor
 */
Ikantam.AjaxCart.AddToCartProductViewLinker = function(controller, config){

    this.controller = controller;
    this.config = config;

};

Ikantam.AjaxCart.AddToCartProductViewLinker.prototype = {

    init    : function(){

        this.validator = new Ikantam.AjaxCart.LinkValidator([
            'checkout/cart/add',
            'checkout/cart/updateItemOptions'
            ],{ 'checkout/cart' : 'iajaxcart/index' }
        );

        this._unbind();
        this._init();


    },

    _unbind : function(){

        var form = jQuery(this.config.selector.form);
        if(form){
            var button = form.find(this.config.selector.button);
            var defaultOnclick;
            if(button && (defaultOnclick = button.attr("defaultOnclick") ) ){
                button.attr("onclick", defaultOnclick);
                button.removeAttr("defaultOnclick");
            }
        }


    },

    _init   : function(){


        /** form initilization */
        var form = jQuery(this.config.selector.form);

        var url = this._isValid(form);
        if(!url){
            return;
        }

        var session = this.controller.getSession();
        (function (self, session, form, url) {
            form.submit(function () {

                var params = form.serialize();
                self.controller.query(session, url, params);
                return false;

            });
        })(this, session, form, url);

        /** button for form submit initilization */
        var button = form.find(this.config.selector.button);

        var defaultOnclick = button.attr("onclick");
        button.attr("defaultOnclick", defaultOnclick);
        button.removeAttr("onclick");

        (function(self, session, form, url){
            button.click(function(){
                var params = form.serialize();
                self.controller.query(session, url, params);
                return false;
            })
        })(this, session, form, url);

    },

    _isValid    : function( elem ){

        if(!elem){
            return false;
        }
        var action = elem.attr('action');
        if( this.validator.isValid(action) ){
            return this.validator.prepareUrl(action);
        }
        return false;

    }

};


Ikantam.AjaxCart.AddToCompareLinker = function( controller, config ){
    this.controller = controller;
    this.config = config;
};

Ikantam.AjaxCart.AddToCompareLinker.prototype = {

    init    : function(){

        this.validator = new Ikantam.AjaxCart.LinkValidator([
            'catalog/product_compare/add'
        ],{ 'catalog/product_compare' : 'iajaxcart/compare' }
        );

        this._unbind();
        this._init();
    },

    _unbind : function(){

    },

    _init   : function(){

        var links = jQuery( this.config.selector.add );

        for(var i = 0; i < links.length; i++){
            var link = jQuery(links[i]);
            var url = link.attr("href");
            if( this.validator.isValid(url) ){
                url = this.validator.prepareUrl(url);
                this.controller.registerEvent(this,link,url,{});
            }
        }
    }

};


Ikantam.AjaxCart.RemoveFromCompareLinker = function( controller, config){
    this.controller = controller;
    this.config = config;
}

Ikantam.AjaxCart.RemoveFromCompareLinker.prototype = {

    init    : function(){

        this.validator = new Ikantam.AjaxCart.LinkValidator([
            'catalog/product_compare/remove'
        ],{ 'catalog/product_compare' : 'iajaxcart/compare' }
        );

        this._unbind();
        this._init();
    },

    _unbind : function(){

    },

    _init   : function(){

        var links = this._getLinks();

        for(var i = 0; i < links.length; i++){
            var link = jQuery(links[i]);
            var url = link.attr("href");
            if( this.validator.isValid(url) ){
                url = this.validator.prepareUrl(url);
                this.controller.registerEvent(this, link, url, {});
            }
        }
    },


    _getLinks   : function(){
        return jQuery(this.config.selector.remove);
    }
}


Ikantam.AjaxCart.ClearAllCompareLinker = function( controller, config){
    this.controller = controller;
    this.config = config;
}

Ikantam.AjaxCart.ClearAllCompareLinker.prototype = {

    init    : function(){

        this.validator = new Ikantam.AjaxCart.LinkValidator([
            'catalog/product_compare/clear'
        ],{ 'catalog/product_compare' : 'iajaxcart/compare' }
        );

        this._unbind();
        this._init();
    },

    _unbind : function(){

    },

    _init   : function(){

        var links = this._getLinks();

        for(var i = 0; i < links.length; i++){
            var link = jQuery(links[i]);
            var url = link.attr("href");
            if( this.validator.isValid(url) ){
                url = this.validator.prepareUrl(url);
                this.controller.registerEvent(this, link, url, {});
            }
        }
    },


    _getLinks   : function(){
        return jQuery(this.config.selector.link);
    }
}


Ikantam.AjaxCart.AddToWishlistLinker = function( controller, config){
    this.controller = controller;
    this.config = config;
}

Ikantam.AjaxCart.AddToWishlistLinker.prototype = {

    init    : function(){

        this.validator = new Ikantam.AjaxCart.LinkValidator([
            'wishlist/index/add'
        ],{ 'wishlist/index' : 'iajaxcart/wishlist' }
        );

        this._unbind();
        this._init();
    },

    _unbind : function(){

    },

    _init   : function(){

        var links = jQuery( this.config.selector.link );

        for(var i = 0; i < links.length; i++){
            var link = jQuery(links[i]);
            var url = link.attr("href");
            if( this.validator.isValid(url) ){
                url = this.validator.prepareUrl(url);
                this.controller.registerEvent(this,link,url,{});
            }
        }
    }

};


Ikantam.AjaxCart.RemoveFromWishlistLinker = function( controller, config){
    this.controller = controller;
    this.config = config;
}

Ikantam.AjaxCart.RemoveFromWishlistLinker.prototype = {

    init    : function(){

        this.validator = new Ikantam.AjaxCart.LinkValidator([
            'wishlist/index/remove'
        ],{ 'wishlist/index' : 'iajaxcart/wishlist' }
        );

        this._unbind();
        this._init();
    },

    _unbind : function(){

    },

    _init   : function(){

        var links = jQuery( this.config.selector.link );

        for(var i = 0; i < links.length; i++){
            var link = jQuery(links[i]);
            var url = link.attr("href");
            if( this.validator.isValid(url) ){
                url = this.validator.prepareUrl(url);
                this.controller.registerEvent(this,link,url,{});
            }
        }
    }

};


/**
 * Link validator
 * @param allowParts Parts allowed in links, for valid check
 * @param replacement Replacement, for prepare links
 * @constructor
 */
Ikantam.AjaxCart.LinkValidator = function(allowParts, replacement){
    this.allowParts = allowParts;
    this.replacement = replacement;
};

Ikantam.AjaxCart.LinkValidator.prototype = {

    isValid : function(url){

        if(!url){
            return false;
        }
        for(var i in this.allowParts){
            if( url.indexOf(this.allowParts[i]) > -1 ){
                return true;
            }
        }
        return false;

    },

    prepareUrl  : function(url){

        for( var replaceable in this.replacement){
            url = url.replace(replaceable, this.replacement[replaceable]);
        }
        return url;

    }
}


