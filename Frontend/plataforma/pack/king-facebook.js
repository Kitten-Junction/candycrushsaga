/**
 * Base namespace for the King library.
 * @const
 */
var king = {};
king.util = {};
king.ext = {};
window['king'] = king;
king.api = king.api || {};king.facebook = king.facebook || {};king.kingdom = king.kingdom || {};/** GENERATED CODE. This code is generated from com.king.platform.kingaccount.AcceptTermsOfServiceResult.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.AcceptTermsOfServiceResult = {
	UNDEFINED : -1,	// "Undefined result"
	OK : 0,	// "Acceptance of the Terms of Service and Privacy Policy processed"
	OK_OUTDATED_VERSION : 1,	// "Acceptance of an outdated version of the Terms of Service and Privacy Policy processed"
	OLDER_VERSION : 2,	// "The version is older than the previously accepted Terms of Service and Privacy Policy"
	INVALID_VERSION : 3	// "The version of the accepted Terms of Service and Privacy Policy is invalid"
};
/** GENERATED CODE. This code is generated from com.king.platform.gamecomb.AppGameCombPurchaseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.AppGameCombPurchaseStatus = {
	UNKNOWN : 0,	// "unknown"
	OK : 1,	// "ok"
	ERROR : 2,	// "error"
	PENDING : 3,	// "pending"
	DELIVERED : 4	// "delivered"
};
/** GENERATED CODE. This code is generated from com.king.platform.mercado.AppMercadoPurchaseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.AppMercadoPurchaseStatus = {
	OK : 0,
	ERROR : 1,
	INVALID_RECEIPT : 2
};
/** GENERATED CODE. This code is generated from com.king.platform.qihoo360.AppQihoo360PurchaseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.AppQihoo360PurchaseStatus = {
	UNKNOWN : 0,	// "unknown"
	OK : 1,	// "ok"
	ERROR : 2,	// "error"
	PENDING : 3	// "pending"
};
/** GENERATED CODE. This code is generated from com.king.platform.facebook.ConnectResponseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.ConnectResponseStatus = {
	LOGIN : 1,
	NEW_USER : 2,
	CHANGED_CORE_USER : 3,
	CORE_USER_MISMATCH : 4,
	ERR_WRONG_PASSWORD : 5,
	ERR_FACEBOOK : 6,
	ERR_KAKAO : 7,
	ERR_EMAIL_MALFORMED : 8,
	ERR_EMAIL_USED : 9,
	ERR_PASSWORD_MALFORMED : 10,
	UNKNOWN : 11,
	ERR_CORE_USER_FORGOTTEN : 12
};
/** GENERATED CODE. This code is generated from com.king.platform.permission.ConsentResponseResultCode.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.ConsentResponseResultCode = {
	OK : 0,	// "The call was successful"
	ALREADY_GRANTED : 1,	// "The permission has already been granted (may be a lower version)"
	INVALID_VERSION : 2,	// "An invalid version was provided"
	OUTDATED_VERSION : 3,	// "An outdated version was provided"
	INVALID_PERMISSION : 4,	// "An invalid permission was provided"
	NOT_GRANTED : 5	// "The permission has not been granted before revoking"
};
/** GENERATED CODE. This code is generated from com.king.platform.permission.ConsentStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.ConsentStatus = {
	GRANTED : 0,	// "Consent granted for current permission version"
	REVOKED : 1,	// "Consent explicitly revoked"
	OUTDATED : 2,	// "Consent granted for previous version of the permission"
	NO_INFO : 3,	// "No previous information on this permission"
	EXPIRED : 4	// "Consent has expired"
};
/** GENERATED CODE. This code is generated from com.king.platform.itemdelivery.DeliveryInfoType.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.DeliveryInfoType = {
	MESSAGE : 0,	// "message"
	IMAGE : 1,	// "image_sd"
	TITLE : 2,	// "title"
	BUTTON_TEXT : 3,	// "button"
	IMAGE_HD : 4,	// "image_hd"
	ACTION : 5	// "action"
};
/** GENERATED CODE. This code is generated from com.king.platform.product.DisplayProduct.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {number} itemType
 * @param {Array.<king.Money>} prices
 * @param {Array.<king.Money>} listPrices
 * @param {number} discountFactorPercent
 * @param {string|null} deliverData
 * @struct
 */
king.DisplayProduct = function (itemType, prices, listPrices, discountFactorPercent, deliverData){ 
    this.itemType = itemType;
    this.prices = prices;
    this.listPrices = listPrices;
    this.discountFactorPercent = discountFactorPercent;
    this.deliverData = deliverData;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.DisplayProductPackage.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {number} type
 * @param {Array.<king.DisplayProduct>} products
 * @param {Array.<king.Money>} prices
 * @param {Array.<king.Money>} listPrices
 * @struct
 */
king.DisplayProductPackage = function (type, products, prices, listPrices){ 
    this.type = type;
    this.products = products;
    this.prices = prices;
    this.listPrices = listPrices;
};
/** GENERATED CODE. This code is generated from com.king.platform.messages.EncodedUrlMessageDto.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {number} id
 * @param {string|null} encodedMessage
 * @struct
 */
king.EncodedUrlMessageDto = function (id, encodedMessage){ 
    this.id = id;
    this.encodedMessage = encodedMessage;
};
/** GENERATED CODE. This code is generated from com.king.platform.mercado3.ErrorCode.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.ErrorCode = {
	NONE : 0,
	UNKNOWN_PRODUCT : 1,
	DEACTIVATED_PRODUCT : 2,
	MISSING_PRICE : 3,
	DELIVERY_EXCEPTION : 4,
	MAX_DELIVERIES : 5,
	VALIDATION : 6,
	PLAYER_MISSING_VC : 7,
	ALREADY_CONFIRMED : 8,
	ALREADY_DELIVERED : 9
};
/** GENERATED CODE. This code is generated from com.king.platform.blobstore.translations.FileType.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.FileType = {
	UNKNOWN : 0,	// "unknown"
	CSV : 1,	// "csv"
	JSON : 2,	// "json"
	PROPERTIES : 3	// "properties"
};
/** GENERATED CODE. This code is generated from com.king.platform.google.GoogleConnectResponseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.GoogleConnectResponseStatus = {
	LOGIN : 1,
	NEW_USER : 2,
	CHANGED_CORE_USER : 3,
	CORE_USER_MISMATCH : 4,
	ERR_WRONG_PASSWORD : 5,
	ERR_GOOGLE : 6,
	ERR_EMAIL_MALFORMED : 7,
	ERR_EMAIL_USED : 8,
	ERR_PASSWORD_MALFORMED : 9,
	UNKNOWN : 10,
	ERR_CORE_USER_FORGOTTEN : 11
};
/** GENERATED CODE. This code is generated from com.king.platform.identity.IdentityResultCode.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.IdentityResultCode = {
	UNDEFINED : 0,	// "Undefined result"
	SUCCESS : 1,	// "Operation successful"
	ERROR_INVALID_PARAMETERS : -1,	// "One or more parameters are invalid"
	ERROR_INVALID_CREDENTIALS : -2,	// "Invalid credentials (e.g. sign-up token, authentication token, e-mail address and password, FB token, ...)"
	ERROR_EXTERNAL : -3,	// "Error while communicating with an external network"
	ERROR_ALREADY_LINKED : -4,	// "Either the account or the credentials are already linked"
	ERROR_ALREADY_USED : -5,	// "Already used (e.g. e-mail address, FB identifier, ...)"
	ERROR_UNLINKED_ACCOUNT : -6	// "The account is not linked"
};
/** GENERATED CODE. This code is generated from com.king.platform.kingdom.kingvites.InviteResult.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.InviteResult = {
	INVALID_STATUS : 0,
	UNKNOWN_FRIEND : 1,
	FRIEND_ALREADY_PLAYING_TO_APP : 2,
	FRIEND_NOT_PLAYING_THROUGH_APPS : 3,
	SENT : 4,
	ASSIGNED_FRIEND : 5
};
/** GENERATED CODE. This code is generated from com.king.platform.rpc.JsonRpcClientAction.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.JsonRpcClientAction = {
	FAIL : 1,
	FAKE : 2
};
/** GENERATED CODE. This code is generated from com.king.platform.rpc.JsonRpcErrorCode.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.JsonRpcErrorCode = {
	FATAL : -32000,	// "Fatal error"
	INVALID_JSON : -32700,	// "Parse error"
	INVALID_REQUEST : -32600,	// "Invalid request"
	METHOD_NOT_FOUND : -32601,	// "Method not found"
	INVALID_PARAMS : -32602,	// "Invalid params"
	INTERNAL_ERROR : -32603,	// "Internal error"
	SERVICE_UNAVAILABLE : -32099,	// "Service Unavailable"
	AUTHENTICATION_ERROR : 2,	// "Authentication error"
	NO_SESSION_KEY_ERROR : 3,	// "No session key error"
	MAX_SIZE_EXCEEDED : 4,	// "Request too large"
	SERVER_BLOCK : 5,	// "Blocked by server"
	USER_FORGOTTEN_ERROR : 6	// "User forgotten error"
};
/** GENERATED CODE. This code is generated from com.king.platform.common.KeyValue.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} key
 * @param {string|null} value
 * @struct
 */
king.KeyValue = function (key, value){ 
    this.key = key;
    this.value = value;
};
/** GENERATED CODE. This code is generated from com.king.platform.kingdom.KingdomConst.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

king.KingdomConst = {
	/** Marker for telling the server that an email address should be generated */
	emailGeneratorMarker : "@GENERATE@",
	/** Email addresses ending with "@u.king.com" are reserved for generated accounts */
	generatedEmailPostfix : "@u.king.com",
	/**
	 Names starting with "king" or "u" (old format) with subsequent digits are reserved for generated names.
	 To check if a name is generated or not both generatedNamePrefix and generatedUsernamePrefix must be checked
	*/
	generatedNamePrefix : "king",
	/**
	 Username is not currently used by the clients,
	 but the prefix is needed to check if Name is generated (see comment on generatedNamePrefix) 
	*/
	generatedUsernamePrefix : "u",
	emailMaxLength : 128,
	nameMinLength : 1,
	nameMaxLength : 32,
	passwordMinLength : 4,
	passwordMaxLength : 128
};
/** GENERATED CODE. This code is generated from com.king.platform.product.KsdkStoreManagerCapabilities.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {boolean} redeem
 * @param {boolean} mobilePricePoints
 * @param {boolean} displayForOutcome
 * @struct
 */
king.KsdkStoreManagerCapabilities = function (redeem, mobilePricePoints, displayForOutcome){ 
    this.redeem = redeem;
    this.mobilePricePoints = mobilePricePoints;
    this.displayForOutcome = displayForOutcome;
};
/** GENERATED CODE. This code is generated from com.king.platform.identity.LinkStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.LinkStatus = {
	UNDEFINED : 0,	// "Undefined result"
	OK : 1,	// "Link is present and working"
	EXPIRED : -1,	// "Link is present but external authentication is expired"
	OUTDATED : -2,	// "Could not refresh data from external network"
	UNAUTHORIZED : -3	// "External network data access needs user permission approval"
};
/** GENERATED CODE. This code is generated from com.king.platform.mercado.MercadoClientStateMetaData.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.MercadoClientStateMetaData = {
	CLIENT_VERSION : 1,	// "client_version"
	CAMPAIGN_PRODUCT_SET_ID : 2,	// "campaign_product_set_id"
	CAMPAIGN_STORE_ID : 3	// "campaign_store_id"
};
/** GENERATED CODE. This code is generated from com.king.platform.product.MercadoProductOrderItem.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {number} storeId
 * @param {number} productId
 * @param {number} productSetId
 * @param {number} version
 * @param {number} receiverCoreUserId
 * @struct
 */
king.MercadoProductOrderItem = function (storeId, productId, productSetId, version, receiverCoreUserId){ 
    this.storeId = storeId;
    this.productId = productId;
    this.productSetId = productSetId;
    this.version = version;
    this.receiverCoreUserId = receiverCoreUserId;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.MobilePricePoints.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} phoneNumber
 * @param {string|null} mobileCountry
 * @param {Array.<king.PricePoint>} pricePoints
 * @struct
 */
king.MobilePricePoints = function (phoneNumber, mobileCountry, pricePoints){ 
    this.phoneNumber = phoneNumber;
    this.mobileCountry = mobileCountry;
    this.pricePoints = pricePoints;
};
/** GENERATED CODE. This code is generated from com.king.platform.money.Money.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {number} cents
 * @param {string|null} currency
 * @struct
 */
king.Money = function (cents, currency){ 
    this.cents = cents;
    this.currency = currency;
};
/** GENERATED CODE. This code is generated from com.king.platform.socialnetwork.Notification.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {Array.<string|null>} recipients
 * @param {string|null} title
 * @param {string|null} body
 * @param {string|null} type
 * @param {king.EncodedUrlMessageDto} encodedUrlMessage
 * @param {string|null} linkText
 * @param {Array.<string|null>} imageUrls
 * @param {string|null} filters
 * @struct
 */
king.Notification = function (recipients, title, body, type, encodedUrlMessage, linkText, imageUrls, filters){ 
    this.recipients = recipients;
    this.title = title;
    this.body = body;
    this.type = type;
    this.encodedUrlMessage = encodedUrlMessage;
    this.linkText = linkText;
    this.imageUrls = imageUrls;
    this.filters = filters;
};
/** GENERATED CODE. This code is generated from com.king.platform.permission.PermissionTypes.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.PermissionTypes = {
	GOOGLE_PERSONALIZED_ADS : 2	// "Permission to show google personalized ads"
};
/** GENERATED CODE. This code is generated from com.king.platform.socialnetwork.Post.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {Array.<string|null>} recipients
 * @param {string|null} title
 * @param {string|null} body
 * @param {string|null} linkText
 * @param {string|null} link
 * @param {string|null} image
 * @param {Array.<king.KeyValue>} parameters
 * @struct
 */
king.Post = function (recipients, title, body, linkText, link, image, parameters){ 
    this.recipients = recipients;
    this.title = title;
    this.body = body;
    this.linkText = linkText;
    this.link = link;
    this.image = image;
    this.parameters = parameters;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.PricePoint.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} id
 * @param {number} payoutAmountInCents
 * @param {number} payerPriceInCents
 * @param {string|null} currency
 * @param {string|null} countryCode
 * @struct
 */
king.PricePoint = function (id, payoutAmountInCents, payerPriceInCents, currency, countryCode){ 
    this.id = id;
    this.payoutAmountInCents = payoutAmountInCents;
    this.payerPriceInCents = payerPriceInCents;
    this.currency = currency;
    this.countryCode = countryCode;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.ProductOrder.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} placement
 * @param {string|null} title
 * @param {string|null} description
 * @param {string|null} imageUrl
 * @param {Array.<king.ProductOrderItem>} orderItems
 * @param {string|null} currency
 * @param {king.MercadoProductOrderItem} mercadoProductOrderItem
 * @struct
 */
king.ProductOrder = function (placement, title, description, imageUrl, orderItems, currency, mercadoProductOrderItem){ 
    this.placement = placement;
    this.title = title;
    this.description = description;
    this.imageUrl = imageUrl;
    this.orderItems = orderItems;
    this.currency = currency;
    this.mercadoProductOrderItem = mercadoProductOrderItem;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.ProductOrderItem.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {number} productPackageType
 * @param {number} receiverCoreUserId
 * @struct
 */
king.ProductOrderItem = function (productPackageType, receiverCoreUserId){ 
    this.productPackageType = productPackageType;
    this.receiverCoreUserId = receiverCoreUserId;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.PurchaseOutcome.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} transactionId
 * @param {number} productPackageType
 * @param {number} quantity
 * @param {boolean} outcomeSuccessful
 * @param {king.DisplayProductPackage} displayProductPackage
 * @param {boolean} isPurchaseForAnotherUser
 * @param {number} mercadoProductId
 * @param {string|null} funnelTxnId
 * @param {string|null} placement
 * @param {number} storeIdentifier
 * @struct
 */
king.PurchaseOutcome = function (transactionId, productPackageType, quantity, outcomeSuccessful, displayProductPackage, isPurchaseForAnotherUser, mercadoProductId, funnelTxnId, placement, storeIdentifier){ 
    this.transactionId = transactionId;
    this.productPackageType = productPackageType;
    this.quantity = quantity;
    this.outcomeSuccessful = outcomeSuccessful;
    this.displayProductPackage = displayProductPackage;
    this.isPurchaseForAnotherUser = isPurchaseForAnotherUser;
    this.mercadoProductId = mercadoProductId;
    this.funnelTxnId = funnelTxnId;
    this.placement = placement;
    this.storeIdentifier = storeIdentifier;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.PurchaseResponse.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} status
 * @param {string|null} error
 * @param {string|null} transactionId
 * @param {boolean} isPurchaseForAnotherUser
 * @struct
 */
king.PurchaseResponse = function (status, error, transactionId, isPurchaseForAnotherUser){ 
    this.status = status;
    this.error = error;
    this.transactionId = transactionId;
    this.isPurchaseForAnotherUser = isPurchaseForAnotherUser;
};
/** GENERATED CODE. This code is generated from com.king.platform.tencent.QqResponseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.QqResponseStatus = {
	LOGIN : 1,
	NEW_USER : 2,
	CHANGED_CORE_USER : 3,
	CORE_USER_MISMATCH : 4,
	ERR_WRONG_PASSWORD : 5,
	ERR_QQ : 6,
	ERR_EMAIL_MALFORMED : 7,
	ERR_EMAIL_USED : 8,
	ERR_PASSWORD_MALFORMED : 9,
	ERR_CORE_USER_FORGOTTEN : 10,
	UNKNOWN : -1
};
/** GENERATED CODE. This code is generated from com.king.platform.product.QuantifiedProductOrder.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} placement
 * @param {number} productPackageType
 * @param {number} receiverCoreUserId
 * @param {number} quantity
 * @param {string|null} currency
 * @param {string|null} mobilePricePointId
 * @struct
 */
king.QuantifiedProductOrder = function (placement, productPackageType, receiverCoreUserId, quantity, currency, mobilePricePointId){ 
    this.placement = placement;
    this.productPackageType = productPackageType;
    this.receiverCoreUserId = receiverCoreUserId;
    this.quantity = quantity;
    this.currency = currency;
    this.mobilePricePointId = mobilePricePointId;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.RedeemOrder.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} placement
 * @param {number} receiverCoreUserId
 * @struct
 */
king.RedeemOrder = function (placement, receiverCoreUserId){ 
    this.placement = placement;
    this.receiverCoreUserId = receiverCoreUserId;
};
/** GENERATED CODE. This code is generated from com.king.platform.product.RedeemResponse.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {string|null} status
 * @param {string|null} error
 * @param {string|null} transactionId
 * @param {number} productPackageType
 * @param {number} quantity
 * @param {boolean} isPurchaseForAnotherUser
 * @struct
 */
king.RedeemResponse = function (status, error, transactionId, productPackageType, quantity, isPurchaseForAnotherUser){ 
    this.status = status;
    this.error = error;
    this.transactionId = transactionId;
    this.productPackageType = productPackageType;
    this.quantity = quantity;
    this.isPurchaseForAnotherUser = isPurchaseForAnotherUser;
};
/** GENERATED CODE. This code is generated from com.king.platform.reward2.RewardCategory.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.RewardCategory = {
	REDEEM_CODE : 1,
	VIDEO_ADS : 2,
	GIFTING_1 : 3
};
/** GENERATED CODE. This code is generated from com.king.platform.socialnetwork.SocialResponse.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @constructor
 * @param {boolean} success
 * @struct
 */
king.SocialResponse = function (success){ 
    this.success = success;
};
/** GENERATED CODE. This code is generated from com.king.platform.store.StoreStatusCodeDto.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.StoreStatusCodeDto = {
	SUCCESS : 0,
	SERVER_ERROR : 1,
	MISSING_JOURNAL : 2
};
/** GENERATED CODE. This code is generated from com.king.platform.store.StoreValidationResultDto.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.StoreValidationResultDto = {
	VALIDATION_NOT_STARTED : 0,
	VALID : 1,
	INVALID : 2,
	PENDING : 3
};
/** GENERATED CODE. This code is generated from com.king.platform.tencent.TencentGuestResponseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.TencentGuestResponseStatus = {
	LOGIN : 1,
	NEW_USER : 2,
	CHANGED_CORE_USER : 3,
	CORE_USER_MISMATCH : 4,
	ERR_WRONG_PASSWORD : 5,
	ERR_TENCENT_GUEST : 6,
	ERR_EMAIL_MALFORMED : 7,
	ERR_EMAIL_USED : 8,
	ERR_PASSWORD_MALFORMED : 9,
	ERR_CORE_USER_FORGOTTEN : 10,
	UNKNOWN : -1
};
/** GENERATED CODE. This code is generated from com.king.platform.twitter.TwitterResponseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.TwitterResponseStatus = {
	LOGIN : 1,
	NEW_USER : 2,
	CHANGED_CORE_USER : 3,
	CORE_USER_MISMATCH : 4,
	ERR_WRONG_PASSWORD : 5,
	ERR_TWITTER : 6,
	ERR_EMAIL_MALFORMED : 7,
	ERR_EMAIL_USED : 8,
	ERR_PASSWORD_MALFORMED : 9,
	UNKNOWN : 10,
	ERR_CORE_USER_FORGOTTEN : 11
};
/** GENERATED CODE. This code is generated from com.king.platform.uno.UnoErrorCode.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.UnoErrorCode = {
	SERVER_ERROR : 100000,
	INTERNAL_SERVER_ERROR : 110000,
	SERVICE_UNAVAILABLE : 120000,
	SERVICE_DISABLED : 121000,
	EXTERNAL_SERVER_ERROR : 130000,
	HTTP_CONNECTION_ERROR : 132000,
	HTTP_REQUEST_ERROR : 133000,
	UMBRELLA_ACCOUNT_LINKING_ERROR : 135000,
	DATABASE_ERROR : 140000,
	INTEGRITY_ERROR : 141000,
	USER_ON_DELETE_EXCEPTION : 142000,
	DATA_INCONSISTENT : 143000,
	UNHANDLED_EXCEPTION : 150000,
	PROXY_CONNECT_EXCEPTION : 160000,
	GATEWAY_UNAVAILABLE_ERROR : 190000,
	INVALID_REQUEST : 210000,
	QUERY_STRING_INVALID : 211000,
	INVALID_PARAMETER_REDIRECTION : 211100,
	PARAMETER_INVALID : 212000,
	PARAMETER_MISSING : 212100,
	PARAMETER_UNKNOWN : 212200,
	PARAMETER_CONFLICT : 212300,
	DATA_INVALID : 213000,
	UNSUPPORTED_MEDIA_TYPE : 213100,
	EMPTY_REQUEST_DATA : 213200,
	FIELD_INVALID : 214000,
	FIELD_MISSING : 214100,
	FIELD_UNKNOWN : 214200,
	FIELD_CONFLICT : 214300,
	USER_NAME_INVALID : 214500,
	PASSWORD_INVALID : 214600,
	DATE_OF_BIRTH_INVALID : 214700,
	SECURITY_ANSWER_INVALID : 214800,
	MAIL_TOKEN_INVALID : 218000,
	PROMOTION_NOT_FOUND : 218200,
	SOCIAL_ACCOUNT_TYPE_INVALID : 218300,
	SOCIAL_ACCOUNT_TOKEN_INVALID : 218400,
	SOCIAL_ACCOUNT_TOKEN_MISSING : 218500,
	UNKNOWN_AUTH_SERVER_APP_ID : 219000,
	USER_DOES_NOT_HAVE_A_B_TOKEN : 219100,
	A_B_TOKEN_AUTH_EXHASUTED : 219200,
	PRE_REGISTRATION_TOKEN_INVALID : 219300,
	PRE_REGISTRATION_CODE_INVALID : 219400,
	USER_PREFERENCE_KEY_INVALID : 219500,
	UNAUTHORIZED : 220000,
	INVALID_TOKEN : 222000,
	EXPIRED_TOKEN : 222100,
	USER_BANNED : 223000,
	USER_LOCKED : 224000,
	EMAIL_NOT_VERIFIED : 225000,
	NOT_FOUND : 230000,
	INVALID_PAGE : 231000,
	USER_NOT_FOUND : 232000,
	TO_S_VERSION_NOT_FOUND : 233000,
	TO_S_CONTENT_NOT_FOUND : 233100,
	USER_NOT_SUBSCRIBED : 234000,
	USER_NOT_SUBSCRIBED_TO_CHANNEL : 235000,
	SUBSCRIPTION_DOES_NOT_EXIST : 236000,
	SECURITY_ANSWER_DOES_NOT_EXIST : 237000,
	USER_NEEDS_TO_ACCEPT_TO_S : 238100,
	QUESTION_DOES_NOT_EXIST : 239000,
	CONFLICT : 240000,
	TO_S_ALREADY_ACCEPTED : 241000,
	TO_S_VERSION_ALREADY_EXISTS : 241100,
	TO_S_CONTENT_ALREADY_EXISTS : 241200,
	EMAIL_ALREADY_VERIFIED : 242000,
	SECURITY_ANSWER_EXISTS : 243000,
	EMAIL_EXISTS : 244000,
	USERNAME_EXISTS : 245000,
	PROMOTION_EXISTS : 246000,
	UMBRELLA_ACCOUNT_MERGE_ERROR : 247000,
	PRE_REGISTRATION_CODE_CONFLICT : 248000,
	FORBIDDEN : 250000,
	METHOD_NOT_ALLOWED : 260000,
	UNKNOWN : -1
};
/** GENERATED CODE. This code is generated from com.king.platform.facebook.UpdateResponseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.UpdateResponseStatus = {
	OK_UPDATED : 1,
	ERR_NO_SESSION : 2,
	ERR_INVALID_ACCESS_TOKEN : 3,
	ERR_FACEBOOK : 4,
	ERR_NO_USER : 5,
	UNKNOWN : 6
};
/** GENERATED CODE. This code is generated from com.king.platform.socialuser.friendcontext.UserListContext.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.UserListContext = {
	MESSAGE_SENDING : 1	// "Sending a message, e.g. life or collaboration request. Identifier: the message\'s type."
};
/** GENERATED CODE. This code is generated from com.king.platform.tencent.WechatResponseStatus.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/** @enum {number} */
king.WechatResponseStatus = {
	LOGIN : 1,
	NEW_USER : 2,
	CHANGED_CORE_USER : 3,
	CORE_USER_MISMATCH : 4,
	ERR_WRONG_PASSWORD : 5,
	ERR_WECHAT : 6,
	ERR_EMAIL_MALFORMED : 7,
	ERR_EMAIL_USED : 8,
	ERR_PASSWORD_MALFORMED : 9,
	ERR_CORE_USER_FORGOTTEN : 10,
	UNKNOWN : -1
};
/** GENERATED CODE. This code is generated from com.king.platform.product.KsdkProductManagerJsApi.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @interface
 */
king.api.KsdkProductManager = function () {
};

/**
 * @param {!Object.<string, string>} parameters
 */
king.api.KsdkProductManager.prototype.init = function (parameters) {
};



/**
 * @param {function(Array.<king.DisplayProductPackage>)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkProductManager.prototype.getAllProductPackages = function (onSuccess, onError) { 
};

/** GENERATED CODE. This code is generated from com.king.platform.product.KsdkStoreManagerJsApi.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @interface
 */
king.api.KsdkStoreManager = function () {
};

/**
 * @param {!Object.<string, string>} parameters
 */
king.api.KsdkStoreManager.prototype.init = function (parameters) {
};



/**
 * @param {function(king.MobilePricePoints)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkStoreManager.prototype.getMobilePricePoints = function (onSuccess, onError) { 
};

/**
 * @param {king.QuantifiedProductOrder} quantifiedProductOrder
 * @param {function(king.PurchaseResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkStoreManager.prototype.purchase = function (quantifiedProductOrder, onSuccess, onError) { 
};

/**
 * @param {king.RedeemOrder} redeemOrder
 * @param {function(king.RedeemResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkStoreManager.prototype.redeem = function (redeemOrder, onSuccess, onError) { 
};

/**
 * @param {string|null} storeIdentifier
 * @param {function(Array.<king.PurchaseOutcome>)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkStoreManager.prototype.getPurchaseOutcomesToDisplay2 = function (storeIdentifier, onSuccess, onError) { 
};

/**
 * @param {function(king.KsdkStoreManagerCapabilities)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkStoreManager.prototype.getCapabilities = function (onSuccess, onError) { 
};

/**
 * @param {string|null} transactionId
 * @param {function(king.PurchaseOutcome)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkStoreManager.prototype.getPurchaseOutcomeToDisplay = function (transactionId, onSuccess, onError) { 
};

/**
 * @param {Array.<king.PurchaseOutcome>} purchaseOutcomes
 * @param {function(void)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkStoreManager.prototype.confirmDisplayedToUser = function (purchaseOutcomes, onSuccess, onError) { 
};

/**
 * @param {function(Array.<king.PurchaseOutcome>)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.KsdkStoreManager.prototype.getPurchaseOutcomesToDisplay = function (onSuccess, onError) { 
};

/** GENERATED CODE. This code is generated from com.king.platform.socialnetwork.SocialNotificationJsApi.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @interface
 */
king.api.Notifications = function () {
};

/**
 * @param {!Object.<string, string>} parameters
 */
king.api.Notifications.prototype.init = function (parameters) {
};



/**
 * @param {king.Notification} notification
 * @param {string|null} trackingType
 * @param {function(king.SocialResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Notifications.prototype.send = function (notification, trackingType, onSuccess, onError) { 
};

/** GENERATED CODE. This code is generated from com.king.platform.socialnetwork.SocialPostJsApi.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @interface
 */
king.api.Posts = function () {
};

/**
 * @param {!Object.<string, string>} parameters
 */
king.api.Posts.prototype.init = function (parameters) {
};



/**
 * @param {string|null} recipient
 * @param {king.Post} post
 * @param {string|null} trackingType
 * @param {function(king.SocialResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Posts.prototype.sendToUser = function (recipient, post, trackingType, onSuccess, onError) { 
};

/**
 * @param {king.Post} post
 * @param {string|null} trackingType
 * @param {function(king.SocialResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Posts.prototype.send = function (post, trackingType, onSuccess, onError) { 
};

/** GENERATED CODE. This code is generated from com.king.platform.product.ProductJsApi.java by com.king.buildtools.codegen.generator.js.JsApiGenerator */

window.king = king || {};

/**
 * @interface
 */
king.api.Product = function () {
};

/**
 * @param {!Object.<string, string>} parameters
 */
king.api.Product.prototype.init = function (parameters) {
};



/**
 * @param {function(boolean)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.isNPPEligible = function (onSuccess, onError) { 
};

/**
 * @param {king.ProductOrder} productOrder
 * @param {function(king.PurchaseResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.buy2 = function (productOrder, onSuccess, onError) { 
};

/**
 * @param {king.RedeemOrder} redeemOrder
 * @param {function(king.RedeemResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.redeem2 = function (redeemOrder, onSuccess, onError) { 
};

/**
 * @param {function(king.MobilePricePoints)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.getMobilePricePoints = function (onSuccess, onError) { 
};

/**
 * @param {king.QuantifiedProductOrder} quantifiedProductOrder
 * @param {function(king.PurchaseResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.buyNpp = function (quantifiedProductOrder, onSuccess, onError) { 
};

/**
 * @param {string|null} transactionId
 * @param {function(king.PurchaseOutcome)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.getPurchaseOutcomeToDisplay = function (transactionId, onSuccess, onError) { 
};

/**
 * @param {Array.<king.PurchaseOutcome>} purchaseOutcomes
 * @param {function(string)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.confirmDisplayedToUser = function (purchaseOutcomes, onSuccess, onError) { 
};

/**
 * @param {king.QuantifiedProductOrder} quantifiedProductOrder
 * @param {function(king.PurchaseResponse)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.buyWithQuantity2 = function (quantifiedProductOrder, onSuccess, onError) { 
};

/**
 * @param {function(Array.<king.PurchaseOutcome>)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.api.Product.prototype.getPurchaseOutcomesToDisplay = function (onSuccess, onError) { 
};

/** 
 * @enum {number} 
 */
king.Capability = {
	AppFriends: 0,
	AllFriends: 1,
	PostToSelf: 10,
	PostToUser: 11,
	NotifyUser: 20
	
	// Add more capabilities starting on 100
};

king.capabilities = function() {
	/** @type {Array.<king.Capability>} */
	var capabilities = [];
	
	/** @param {...king.Capability} capability */
	var add = function(capability) {
		for (var i=0;i<arguments.length;i++) {
			var C = arguments[i];
			if (capabilities.indexOf(C) < 0) {
				capabilities.push(C);
			}		
		}
	};
	
	/** @param {king.Capability} capability */
	var isSupported = function(capability) {
		return capabilities.indexOf(capability) >= 0;
	};
	/** @return {Array.<king.Capability>} */
	var list = function() {
		return capabilities;
	};
	
	return {
		add : add,
		supports : isSupported,
		list : list
	};
}();/** 
 * @enum {number} 
 */
king.EventType = {
	SOCIAL_INIT: 1,
	HIDE_CLIENT: 2,
	SHOW_CLIENT: 3,
	CLIENT_LOADED: 4,
	FATAL: 100,
	STORAGE_FAIL: 199
};

/** @typedef {function(king.Event)} */ 
king.EventListener;

/**
 * @constructor 
 * @param {king.EventType} type
 * @param {{cause: string,data}} data
 */
king.Event = function (type, data) {
	/** @type {king.EventType} */
	this.type = type;
	
	/** @type {{cause: string,data}} */
	this.data = data;
}

king.eventBus = function() {
	/** @type {Object.<number, Array.<king.EventListener>>} */
	var eventListeners = {};
	
	/**
	 * @param {king.EventType} eventType
	 * @param {king.EventListener} listener
	 */
	var addListener = function(eventType, listener) {
		if (eventListeners[eventType] == undefined) {
			eventListeners[eventType] = [];
		}
		eventListeners[eventType].push(listener);
	};

	/**
	 * @param {king.EventType} eventType
	 * @param {king.EventListener} listener
	 */
	var removeListener = function(eventType, listener) {
		if (eventListeners[eventType] == undefined) {
			return;
		}
		
		var index = eventListeners[eventType].indexOf(listener);
		if (index > 0) {
			eventListeners[eventType].splice(index, 1);
		}		
	};

	/**
	 * @param {king.Event} event
	 */
	var post = function(event) {
		king.debug("EventBus: Posting",event);
		if (eventListeners[event.type] == undefined) {
			return;
		}
		var listeners = eventListeners[event.type];
		for (var index in listeners) {
			listeners[index](event);			
		}	
	};	
	return {
		addListener : addListener,
		removeListener : removeListener,
		post : post
	};
}();

if (typeof (ExternalInterfaceRpc) === "undefined") {
	window.ExternalInterfaceRpc = {};
}

window['ExternalInterfaceRpc'] = function() {
	var flashObject = 'undefined';

	var init = function(aflashObject) {
		flashObject = aflashObject;
	};

	var receive = function(object) {
		if (object.method != undefined) {
			if (object.id != undefined) {
				lookup(object.method).apply(lookupParent(object.method), object.params.concat(function(result) {
					reply({
						id : object.id,
						result : result
					});
				}).concat(function(error) {
					reply({
						id : object.id,
						error : error
					});
				}));
			} else {
				lookup(object.method).apply(lookupParent(object.method), object.params);
			}
		} else {
			throw new Error("method not specified");
		}
	};

	var reply = function(response) {
		try {
			flashObject.externalInterfaceRpcReceive(response);
		} catch (e) {
			king.console("failed to respond:", e);
		}
	};

	var lookup = function(name) {
		var temp = name.split(".");
		var target = window;
		for (index = 0; index < temp.length; index++) {
			target = target[temp[index]];
		}
		return target;
	};
	
	var lookupParent = function(name) {
		var temp = name.split(".");
		var target = window;
		for (index = 0; index < temp.length-1; index++) {
			target = target[temp[index]];
		}
		return target;
	};	

	/** @constructor */
	function ExternalInterfaceRpc() {};
	ExternalInterfaceRpc.prototype = {
		init : init,
		receive : receive
	};
	return new ExternalInterfaceRpc();
}();

// export symbol(s) to enable compression
window['ExternalInterfaceRpc'] = ExternalInterfaceRpc;
king.fs = function() {
	// compatibility 
	window.URL = window.URL || window.webkitURL;
	window.requestFileSystem = window.requestFileSystem || window.webkitRequestFileSystem;
	window.resolveLocalFileSystemURL = window.resolveLocalFileSystemURL || window.webkitResolveLocalFileSystemURL;
	window.storageInfo = window.storageInfo || window.webkitStorageInfo;

	var LOG = "king.fs: ";
	var SEPARATOR = "/";
	var fileSystem = null;
	var cache = {};
	var VOID = function(){};

	var supported = window.requestFileSystem;
	var rejected = false;
	var userInput = false;
	
	var quotaExceededWrapper = function(callback) {
		return function(error) {
			if (error.hasOwnProperty("code") && error.code == QUOTA_EXCEEDED_ERR) {
				king.console(LOG + "Quota exceeded disabling cache");
				supported = false;
				
			}
			callback(error);
		};		
	};
	
	var fileToArrayBuffer = function(blob, successCallback, errorCallback) {
		var reader = new FileReader();
		reader.onload = function(e) {
			successCallback(e.target.result);
		};
		reader.onerror = errorCallback;
		reader.readAsArrayBuffer(blob);
	};
	
	/**
	 * @param {?} directory
	 * @param {function(?)} successCallback
	 * @param {function(?)} errorCallback
	 */	
	var iterate = function(directory, successCallback, errorCallback) {
		if (directory.isFile) {
			king.debug(LOG + "Iterate was called on a file");
			return;
		}
		var entries = [];
		var reader = directory.createReader();
		var readEntries = function() {
			reader.readEntries(function(result) {
				if (!result.length) {
					entries.sort(function(a, b) {
						return a.name < b.name ? -1 : b.name < a.name ? 1 : 0;
		            });
					successCallback(entries);
		          } else {
		            entries = entries.concat(king.util.toArray(result));
		            readEntries();	
				}
			}, errorCallback);
		}
		readEntries();
	};
	
	/**
	 * @param {function()} successCallback
	 * @param {function(?)} errorCallback
	 */
	var treewalk = function(successCallback, errorCallback) {
		var started = 1;
		var stopped = 0;
		var recurse = function(directory, successCallback, errorCallback) {
			iterate(directory, 
					function(result) {
						for (var i=0;i<result.length;i++) {
							var entry = result[i];
							if (entry.isDirectory) {
								cache[entry.fullPath] = entry;
								successCallback(++started, stopped);
								recurse(entry, successCallback, errorCallback);
								continue;
							}
							// check that the filesize is bigger than 0
							var inner = function(entry) {
								entry.file(function(file) {
									if (file.size <= 0) {
										king.debug("File " + entry.fullPath + " is 0 bytes");
										entry.remove(VOID,VOID);
										return;
									}
									cache[entry.fullPath] = entry;
								}, errorCallback);
							};
							inner(entry);
						}
						
						successCallback(started, ++stopped);
					},
					errorCallback
			);
		};
		recurse(fileSystem.root, function(started, stopped) {
			if (started == stopped) {
				successCallback();
			}
		}, errorCallback);
	};
	
	/**
	 * @param {number} storage
	 * @param {function()} successCallback
	 * @param {function(?)} errorCallback
	 */
	var init = function(storage, successCallback, errorCallback) {
		try {
			// another test for support;
			new Blob([new Uint8Array([])]);
		} catch (error) {
			supported = false;
		}

		
		if (!supported) {
			king.debug(LOG + "HTML5 FileSystems API is not supported");
			errorCallback("HTML5 FileSystems API is not supported");
			return;
		}
		
		if (fileSystem != null) {
			king.debug(LOG + "FileSystem already initialized");
			errorCallback("FileSystem already initialized");
			return;
		}
		
		king.debug(LOG + "Requesting " + storage + " bytes of persistent storage");
		
		rejected = false;
		storageInfo.queryUsageAndQuota(storageInfo.TEMPORARY,
				function(result, quota) {
					king.debug(LOG + "usage: " + result +" bytes, available: " + quota + " bytes");
				}, 
				function(error) {
					king.console(LOG + error);
				}
		);
		
		requestFileSystem(storageInfo.TEMPORARY, storage, 
			function(fs) {
				fileSystem = fs;	
				treewalk(function() {
					king.debug(LOG + "HTML5 FileSystems API initialized.");
					successCallback();							
				}, errorCallback); 
			}, function(error) {						
				supported = false;
				errorCallback(error);
			}
		);
	};
	
	/**
	 * @param {string} path
	 * @return {boolean}
	 */	
	var inCache = function(path) {
		if (!supported || cache[path] == undefined) {
			return false;
		}
		return true;
	};

	/**
	 * @param {string} path
	 * @param {function(?)} successCallback
	 * @param {function(?)} errorCallback
	 */
	var open = function(path, successCallback, errorCallback) {
		var inner = function(directory, path, incrementalPath) {
			var next = path.indexOf(SEPARATOR);
			var last = path.lastIndexOf(SEPARATOR);
			
			if (next < 0 && last < 0) {
				// this is a file
				filename = path.replace(SEPARATOR, "");
				directory.getFile(filename, {create:true, exclusive:false}, 
					function(fileEntry) {
						cache[fileEntry.fullPath] = fileEntry;
						successCallback(fileEntry);
					},
					errorCallback
				);
			} else {
				// this is a directory
				var name = path.substr(0, next);
				directory.getDirectory(name, {create:true},
					function(dirEntry) {
						cache[dirEntry.fullPath] = dirEntry;
						
						// recurse
						if (path.length > next) {
							inner(dirEntry, path.substr(next+1), dirEntry.fullPath);
						} else {
							successCallback(dirEntry);
						}					
					},
					errorCallback
				);
			}
		};

		if (inCache(path)) {
			successCallback(cache[path]);
			return;
		}
		if (path.charAt(0) == '/') {
			path = path.substr(1);
		}
		inner(fileSystem.root, path, "");
	};
	
	/**
	 * @param {string} path
	 * @param {?} blob
	 * @param {function(?)} successCallback
	 * @param {function(?)} errorCallback
	 */
	var write = function(path, blob, successCallback, errorCallback) {
		open(path,
				function(fileEntry) {
					if (fileEntry.isDirectory) {
						king.console(LOG + "Trying to write to a directory");
						return;
					}
					fileEntry.createWriter(function(writer) {
				        writer.onwriteend = function(e) { 
				        	king.debug(LOG + "File written to storage", e);
				        	successCallback(fileEntry);
				        };
				        writer.onerror = errorCallback;
				        writer.write(blob, {type: 'application/octet-stream'});					        
				    }, errorCallback);
				}, 
				errorCallback
		);
	}
	
	/**
	 * @param {Array.<string>} validPaths
	 */
	var validate = function(validPaths) {
		// transform to a better format
		var filesToKeep = {};
		for(var i=0;i<validPaths.length;i++) {
			filesToKeep[validPaths[i]] = true;
		}
		for (var file in cache) {
			var entry = cache[file];
			if (entry.isDirectory) {
				continue;
			}
			if (!filesToKeep[file]) {
				var inner = function(entry) {
					king.debug(LOG + "Removing old file ", entry.fullPath);
					entry.remove(
							function() {
								king.debug(LOG + "Removed file", entry.fullPath);
								delete cache[file];
							}, 
							function() {
								king.debug(LOG + "Can't remove file", entry.fullPath);
							}
					);
				};
				inner(entry);
			}	
		}
	}
		
	return {
		supported : function() {return supported != undefined;},
		rejected : function() {return rejected;},
		init : init,
		open : open,
		write : write,
		validate : validate,
		inCache : inCache,
		fileToArrayBuffer : fileToArrayBuffer
	};
}();

/**
 * @interface
 */
function IPlataforma() {};

/**
 * @param {!Object.<string, string>} args
 */
IPlataforma.prototype.init = function(args) {};

/**
 * @param {!string} s
 * @return {string}
 */
IPlataforma.prototype.escapeJavascriptString = function(s) {};

/**
 * @param {!Object} mergeObject
 * @param {!Object} srcObject
 * @param {boolean} deepCopy
 * @return {Object}
 */
IPlataforma.prototype.merge = function (mergeObject, srcObject, deepCopy) {};

/**
 * @return {!Object}
 */
IPlataforma.prototype.ClientHealthTracking = function() {};
/** @typedef {function(Object)} */
king.Callback;

/** @typedef {Error} */
king.Error;

/** @type {boolean} */
king.debugMode = true;

/** @type {Object} */
king.rpc = null;

/** @type {IPlataforma} */
king.plataforma = null;

/** @type {king.api.Social} */
king.social = null;

/** @type {string|null} */
king.gameId = null;

/** @type {string|null} */
king.pauseId = null;

/** @type {king.UserInfo} */
king.user = null;

king.console = function() {
	// Create console methods if someone should to use them by mistake
	if (typeof (console) === "undefined") {
		window['console'] = {};
		var logMethods = ["log","info","warn","error","assert","dir","clear","profile","profileEnd"];	
		for (var i=0;i<logMethods.length;i++) {
			window['console'][logMethods[i]] = function() { };
		}
	}

	/**
	 * @param {...*} args 
	 */
	return function(args) {
		if (typeof (console) === "undefined") {
			return;
		}
		if (typeof(console.log.apply) === "undefined") {
			// If apply isn't supported (IEx) then do some if-magic
			if (arguments.length == 1) {
				console.log(arguments[0]);	
			} else if (arguments.length == 2) {
				console.log(arguments[0], " ", arguments[1]);
			} else if (arguments.length == 3) {
				console.log(arguments[0], " ", arguments[1], " ", arguments[2]);
			} else {
				console.log(arguments);
			}
			return;
		}
		console.log.apply(console, arguments);
	}
}();


/** @param {...*} args */
king.debug = function(args) {
	if (king.debugMode) {
		king.console.apply(king, arguments);
	}
};

/** @return {boolean} */
king.available = function() {
	return true;
}

/** @return {?} */
king.game = function() {
	return window[king.gameId] || document[king.gameId];
}

/**
 * @param {!Object.<string,Object>} parameters
 */
king.init = function(parameters) {
	king.util.require(parameters, 'userId');
	king.util.require(parameters, 'sessionKey');
	
	king.user = new king.UserInfo(parameters.userId, null, parameters.sessionKey);
	
	king.pauseId = parameters.pauseId || "pause";
	king.gameId = parameters.gameId || "game";
	
	king.rpc = new JsonRpcEndpoint("/rpc/ClientApi?_session=" + parameters.sessionKey);
	king.plataforma = window['Plataforma'];
	king.social = new king.Social();
	
	king.plataforma.init(parameters);
	king.social.init(parameters);
	king.resource.init(parameters);
	
	king.exportApis();
	
	king.eventBus.addListener(king.EventType.CLIENT_LOADED, function(event) {
		$('#'+king.pauseId).css({'position':'absolute', 'top':'0px', 'z-index':500})
		$('#'+king.gameId).css({'position':'absolute', 'top':'0px', 'z-index':600, 'visibility': 'visible !important'}); 
	});
};

/** @param {*} data */
king.onRpcSuccess = function(data) {	
	king.debug("RPC ok:", data);
};

/** @param {*} data */
king.onRpcError = function(data) {
	king.debug("RPC failed:", data);
};

king.exportApis = function() {
	king.util.exportApi('SocialNotificationJsApi', king.social.notification);
	king.util.exportApi('SocialPostJsApi', king.social.post);
	king.util.exportApi('SocialUserJsApi', king.social);
	king.util.exportApi('ProductJsApi', king.social.product);
	
	king.util.exportApi('AuthenticationJsApi', {
		authenticate : function(successCallback, errorCallback) {
			try {				
				king.social.authenticate(function(sessionKey) {
					if (sessionKey) {
						king.user.sessionKey = sessionKey;
						king.rpc.setUrl("/rpc/ClientApi?_session=" + sessionKey);
					}
					successCallback(sessionKey);
				});
			} catch (error) {
				king.console("Error during reauthentication", error);
				errorCallback(error);
			}
		}
	});
	
	king.util.exportApi('ResourceLoaderJsApi', {
		init : king.resource.init,
		loadBase64 : king.resource.loadBase64,
		onInit : function(successCallback, errorCallback) {
			king.resource.onInit(successCallback);
		},
		validate : function(validFiles, successCallback, errorCallback) {
			king.resource.validate(validFiles);
			successCallback();
		},
		supported : function(successCallback, errorCallback) {
			successCallback(king.resource.supported());
		}
	});
};/**
 * @constructor
 * @implements {king.api.KsdkProductManager}
 */
king.KsdkProductManager = function () {
};

/**
 * @param {!Object.<string, string>} parameters
 */
king.KsdkProductManager.prototype.init = function (parameters) {
};


/**
 * @param {function(Array.<king.DisplayProductPackage>)} onSuccess 
 * @param {function(king.ApiError)} onError 
 */
king.KsdkProductManager.prototype.getAllProductPackages = function (onSuccess, onError) {
	
	king.rpc.remoteCall('ProductApi.getAllProductPackages', [], onSuccess, onError); 
	
};


var Link = {};
Link.stringify = function(base, params) {
	var url = base;
	var paramNum = 0;
	for ( var param in params) {
		if (params.hasOwnProperty(param)) {
			url += (paramNum == 0 ? "?" : "&");
			url += param + "=" + encodeURIComponent(params[param]);
			paramNum++;
		}
	}
	return url;
};

window['Link'] = Link;

// hack to ensure JSON.stringify will exist
var JSON = JSON || {};
JSON.stringify = JSON.stringify || function(obj) {
	var t = typeof (obj);
	if (t != "object" || obj === null) {
		// simple data type
		if (t == "string")
			obj = '"' + obj + '"';
		return String(obj);
	} else {
		// recurse array or object
		var n, v, json = [], arr = (obj && obj.constructor == Array);
		for (n in obj) {
			v = obj[n];
			t = typeof (v);
			if (t == "string")
				v = '"' + v + '"';
			else if (t == "object" && v !== null)
				v = JSON.stringify(v);
			json.push((arr ? "" : '"' + n + '":') + String(v));
		}
		return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
	}
};

window['Plataforma'] = function() {
	var sessionKey = 'uninitialized';

	var init = function(args) {
		king.console('Plataforma.init() args=', args);
		sessionKey = args.sessionKey;
	};

	/**
	 * Create a javascript string. Escape " and '.
	 * 
	 * @param s
	 *            String string to be escaped
	 * @return String escaped string
	 */
	var escapeJavascriptString = function(s) {
		if (s == null || typeof s == 'undefined') {
			return s;
		}

		var re = new RegExp('([\'\"])', 'g');
		s = s.replace(re, '\\$1');
		s = s.replace(/\n/, '\\n');
		s = s.replace(/\r/, '\\r');

		return s;
	};

	/**
	 * Create a new objects with all values from srcObject and mergeObject.
	 * <p>
	 * Properties present in srcObject will override values in mergeObject
	 * </p>
	 * 
	 * @param mergeObject
	 * @param srcObject
	 * @return a new object
	 */
	var merge = function(mergeObject, srcObject, deepCopy) {
		var obj = {};

		if (typeof deepCopy == "undefined") {
			$.extend(obj, mergeObject, srcObject);
		} else {
			$.extend(deepCopy, obj, mergeObject, srcObject);
		}

		return obj;
	};

	var ClientHealthTracking = function() {
		var clientStartupStage = function(clientType, stage) {
			king.rpc.remoteCall("ClientHealthTracking.clientStartupStage2", [ clientType, stage ], function(result) {
			}, function(error) {
				king.console('Error calling ClientHealthTracking.clientStartupStage():' + error)
			});
		};

		var clientLoadProgress = function(clientType, bytesLoaded) {
			king.rpc.remoteCall("ClientHealthTracking.clientLoadProgress2", [ clientType, bytesLoaded ], function(result) {
			}, function(error) {
				king.console('Error calling ClientHealthTracking.clientLoadProgress():' + error)
			});
		};

		var flashClientInformation = function(clientType, version, manufacturer, os, screenWidth, screenHeight) {
			king.rpc.remoteCall("ClientHealthTracking.clientInformation2", [ clientType, version, manufacturer, os,
					screenWidth, screenHeight ], function(result) {
			}, function(error) {
				king.console('Error calling ClientHealthTracking.flashClientInformation():' + error)
			});
		};

		var clientException = function(clientType, text) {
			king.rpc.remoteCall("ClientHealthTracking.clientException2", [ clientType, text ], function(result) {
			}, function(error) {
				king.console('Error calling ClientHealthTracking.clientException():' + error)
			});
		}

		return {
			clientStartupStage : clientStartupStage,
			clientLoadProgess : clientLoadProgress,
			flashClientInformation : flashClientInformation,
			clientException : clientException
		};
	}();

	/**
	 * @constructor
	 * @implements {IPlataforma}
	 */
	function Plataforma() {};
	Plataforma.prototype = {
		init : init,
		escapeJavascriptString : escapeJavascriptString,
		merge : merge,
		ClientHealthTracking : ClientHealthTracking
	};
	return new Plataforma();
}();

/**
 * AdsDecisionsHandler singleton.
 */
window['AdsDecisionsHandler'] = function() {
	var mPredefinedCriterias = {};
	var mGameCriterias = {};
	var mPlacements = {};

	var sizeOfObject = function(obj) {
		var counter = 0;

		for ( var k in obj) {
			counter++;
		}

		return counter;
	};

	var isString = function(obj) {
		return (typeof obj == "string") && ((obj.length > 0));
	};

	var init = function(jsonObj) {
		// verify json obj
		if (sizeOfObject(jsonObj) && sizeOfObject(jsonObj.predefinedCriterias) && sizeOfObject(jsonObj.gameCriterias)
				&& sizeOfObject(jsonObj.placements)) {
			// all good, continue
		} else {
			king.console("AdsDecisionsHandler.init() failed with object:", jsonObj);
			return;
		}

		mPredefinedCriterias = jsonObj.predefinedCriterias;
		mGameCriterias = jsonObj.gameCriterias;
		mPlacements = jsonObj.placements;
	};

	var debug = function() {
		king.console("AdsDecisionsHandler.debug()", getMembers());
	};

	var getMembers = function() {
		return {
			predefinedCriterias : mPredefinedCriterias,
			gameCriterias : mGameCriterias,
			placements : mPlacements
		}
	};

	/**
	 * @param placementName
	 *            String
	 * @param gameCriterias
	 *            Associative-Array {}
	 * @return Associative-Array {status:Boolean, errorMessage:String}
	 */
	var getWatchAd = function(placementName, gameCriterias) {
		king.debug("AdsDecisionsHandler.getWatchAd() called:", placementName, gameCriterias);

		// verify placement name
		if (isString(placementName) && mPlacements[placementName]) {
			// all good, continue
		} else {
			king.console("AdsDecisionsHandler.getWatchAd() placement name does not exist:", placementName);

			return {
				status : false,
				errorMessage : "Placement name '" + placementName + "' does not exist!"
			};
		}

		// verify game criterias
		if (sizeOfObject(gameCriterias)) {
			for ( var c in gameCriterias) {
				// the criteria sent in by the game must exist / be defined
				if (mGameCriterias[c]) {
					// all good, continue
				} else {
					king.console("AdsDecisionsHandler.getWatchAd() game criteria does not exist:", c);

					return {
						status : false,
						errorMessage : "Game criteria '" + c + "' does not exist!"
					};
				}
			}
		}

		var resultOfDecision = false;
		var placementDecisionCode = mPlacements[placementName];

		// logConsole("Original Code:", placementDecisionCode);

		// compile code
		try {
			// search & replace all predefined criterias
			for ( var criteriaKey in mPredefinedCriterias) {
				// the \\ is used to escape the $ sign which is a group reference
				var regExp = new RegExp("\\" + criteriaKey, "g");
				var criteriaValue = mPredefinedCriterias[criteriaKey];
				placementDecisionCode = placementDecisionCode.replace(regExp, criteriaValue);
			}

			// search & replace all game criterias
			if (sizeOfObject(gameCriterias)) {
				for ( var criteriaKey in gameCriterias) {
					// the \\ is used to escape the $ sign which is a group reference
					var regExp = new RegExp("\\" + criteriaKey, "g"); 
					var criteriaValue = gameCriterias[criteriaKey];
					placementDecisionCode = placementDecisionCode.replace(regExp, criteriaValue);
				}
			}
			// run code
			resultOfDecision = eval(placementDecisionCode + "\nTestDecision();");
		} catch (e) {
			king.console("AdsDecisionsHandler.getWatchAd() exception caught on eval():", e);
			return {
				status : false,
				errorMessage : "Exception caught on eval(): " + e.message
			};
		}

		// return all good
		return {
			status : resultOfDecision,
			errorMessage : null
		}
	};

	/**
	 * @constructor
	 */
	function AdDecisionHandler() {};
	AdDecisionHandler.prototype = {
		init : init,
		debug : debug,
		getMembers : getMembers,
		getWatchAd : getWatchAd
	};
	return new AdDecisionHandler();
}();

// export symbol(s) to enable compression
window['Link'] = Link;
window['JSON'] = JSON;
window['Plataforma'] = Plataforma;
king.resource = function() {

	var cdn = null;
	var initialized = false;
	var initCallbacks = [];
		
	/** @type {function(Object)} */
	var resourceError = function(data) {
		king.debug("ResourceLoader Error: ", data);
	};
	
	var removeCDN = function(url) {
		if (cdn == url.substr(0, cdn.length)) {
			return url.substr(cdn.length);
		}
		return url;
	};
	
	/**
	 *  @param {!Object.<string,string>} parameters
	 */
	var init = function(parameters) {
		king.util.require(parameters, 'cdn');
		king.util.require(parameters, 'storage');
		
		cdn = parameters.cdn;
		
		var runCallbacks = function() {
			if (initCallbacks && initCallbacks.length > 0) {
				for (var i=0;i<initCallbacks.length;i++) {
					initCallbacks[i]();
				}
			}
		}
		
		king.fs.init(parameters.storage, 
				function() {
					initialized = true;
					runCallbacks();
				},
				function(error) {
					initialized = true;
					runCallbacks();
					if (!king.fs.rejected()) {
						resourceError(error);
						return
					}
					king.debug("ResourceLoader: User declined quota request");
					king.eventBus.post(new king.Event(king.EventType.STORAGE_FAIL, {cause:"user", data:error}));
				}
		);
	};
	
	var onInit = function(callback) {
		if (initialized) {
			callback(true);
		} else {
			initCallbacks.push(function(){callback(true);});
		}
	};

	/**
	 * @param {string} vpath
	 * @param {function(?)} successCallback
	 * @param {function(?)} errorCallback
	 */
	var load = function(vpath, successCallback, errorCallback) {
		try {
			vpath = removeCDN(vpath);
			
			if (king.fs.inCache(vpath)) {
				// load from Filesystem API
				king.debug("ResourceLoader: Loading from cache");
				king.fs.open(vpath, 
					function(fileEntry) {
						fileEntry.file(successCallback, errorCallback);
					},
					errorCallback
				);
				return;
			}
			
			// load from CDN
			king.debug("ResourceLoader: Fetching file " + cdn + vpath);
			king.util.fetchFile(cdn + vpath, function(response) {
				king.debug('ResourceLoader: File feetched', response);
				var blob = new Blob([new Uint8Array(response)]);
				if (king.fs.supported()) {
					king.fs.write(vpath, blob, successCallback, errorCallback);
				} else {
					successCallback(blob);	
				}
			});
		} catch(error) {
			king.debug("ResourceLoader: Load failed with error: ", error);
			errorCallback(error);
		}
	};
	
	/**
	 * @param {string} vpath
	 * @param {function(string)} successCallback
	 * @param {function(?)} errorCallback
	 */
	var loadBase64 = function(vpath, successCallback, errorCallback) {
		try {
			vpath = removeCDN(vpath);
			
			var convert = function(blob) {
				king.fs.fileToArrayBuffer(blob, 
					function(buffer) {
						successCallback(king.util.base64encode(buffer));
					}, 
					errorCallback);
			};
			
			if (king.fs.inCache(vpath)) {
				// load from Filesystem API
				king.debug("ResourceLoader: Loading from cache");
				king.fs.open(vpath, 
					function(fileEntry) {
						fileEntry.file(function(file) {
							convert(file);
						});				
					},
					errorCallback
				);
				return;
			}
			
			// load from CDN
			king.debug("ResourceLoader: Fetching file " + cdn + vpath);
			king.util.fetchFile(cdn + vpath, function(response) {
				king.debug('ResourceLoader: File feetched', response);
				var buffer = new Uint8Array(response);
				
				if (king.fs.supported()) {
					king.fs.write(vpath, new Blob([buffer]), 
						function(fileEntry) {
							fileEntry.file(function(file) {
								convert(file);
							});
						}, 
						errorCallback
					);
				} else {
					successCallback(king.util.base64encode(buffer));
				}
			});
		} catch(error) {
			king.debug("ResourceLoader: loadBase64 failed with error: ", error);
			errorCallback(error);
		}
	};

	/**
	 * @param {Array.<string>} validFiles
	 */
	var validate = function(validFiles) {
		king.fs.validate(validFiles);
	};

	return {
		init : init,
		onInit : onInit,
		load : load,
		loadBase64 : loadBase64,
		validate : validate,
		supported : king.fs.supported
	};	
}();
/**
 * @constructor  
 * @param {number} userId
 * @param {?string} externalId
 * @param {string} sessionKey
 */
king.UserInfo = function(userId, externalId, sessionKey) {
	this.userId = userId;
	this.externalId = externalId;
	this.sessionKey = sessionKey;
};

king.UserInfo.prototype = {
		/** @type {number} */	
		userId : 0,
		/** @type {?string} */	
		externalId : null,
		/** @type {string} */	
		sessionKey : ""
};
/**
 * Export an API to window.
 *
 * @param {string} api
 * @param {?} methods
 */
king.util.exportApi = function (api, methods) {
    king.debug("Exporting API: " + api);
    window[api] = methods;
};

/**
 * @param {!Object.<string,*>} parameters
 * @param {string} name
 */
king.util.require = function (parameters, name) {
    if (parameters[name] == undefined) {
        throw new Error("Missing init parameter '" + name + "'");
    }
}

/**
 * @param {!Object.<string,*>} parameters
 * @param {string} name
 * @param {*} defval
 */
king.util.optional = function (parameters, name, defval) {
    if (parameters[name] == undefined) {
        parameters[name] = defval;
    }
}

/**
 * @param {string} url
 * @param {function(?)} callback
 */
king.util.fetchFile = function (url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'arraybuffer';
    xhr.onload = function (e) {
        callback(xhr.response);
    }
    xhr.send();
}

king.util.toArray = function (list) {
    return Array.prototype.slice.call(list || [], 0);
};

/**
 * @param {?} raw
 * @return {string}
 */
king.util.base64encode = function (raw) {
    var base64 = '';
    var encodings = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    var bytes = new Uint8Array(raw);
    var byteLength = bytes.byteLength;
    var byteRemainder = byteLength % 3;
    var mainLength = byteLength - byteRemainder;
    var a, b, c, d;
    var chunk;

    // Main loop deals with bytes in chunks of 3
    for (var i = 0; i < mainLength; i = i + 3) {
        // Combine the three bytes into a single integer
        chunk = (bytes[i] << 16) | (bytes[i + 1] << 8) | bytes[i + 2];
        // Use bitmasks to extract 6-bit segments from the triplet
        a = (chunk & 16515072) >> 18; // 16515072 = (2^6 - 1) << 18
        b = (chunk & 258048) >> 12; // 258048 = (2^6 - 1) << 12
        c = (chunk & 4032) >> 6; // 4032 = (2^6 - 1) << 6
        d = chunk & 63; // 63 = 2^6 - 1
        // Convert the raw binary segments to the appropriate ASCII encoding
        base64 += encodings[a] + encodings[b] + encodings[c] + encodings[d];
    }
    return base64;
};

/**
 * If the social container is initialized call the callback. Otherwise wait until it's initialized
 * and then do the call.
 * @param {function()} callback
 */
king.util.callAfterInit = function (callback) {
    if (king.social != undefined && king.social.isInitialized()) {
        callback();
    } else {
        // If the social container haven't been initialized after 60000 ms try to make the call anyway.
        var handle = setTimeout(callback, 60000);
        // if the social container isn't initialized
        var delayed = function (event) {
            clearTimeout(handle);
            king.eventBus.removeListener(delayed);
            callback();
        };
        king.eventBus.addListener(king.EventType.SOCIAL_INIT, delayed);
    }
};

/**
 * @param {Array.<king.KeyValue>} parameters
 * @return {Object.<string,Object>}
 */
king.util.parameterMap = function (parameters) {
    var result = {};
    for (var i = 0; i < parameters.length; i++) {
        var entry = parameters[i];
        if (entry.key != "actionName") {
            result[entry.key] = entry.value;
        }
    }
    return result;
};


/**
 * @param {Array.<king.KeyValue>} parameters
 * @return {string|null}
 */
king.util.actionName = function (parameters) {
    for (var i = 0; i < parameters.length; i++) {
        var entry = parameters[i];
        if (entry.key == "actionName") {
            return entry.value;
        }
    }
    return null;
};/**
 * @constructor
 * @param {number} code
 * @param {string} message
 * @struct
 */
king.ApiError = function (code, message) {
    this.code = code;
    this.message = message;
};


/** @enum {number} */
king.ApiErrorCode = {
    INTERNAL_ERROR: -60001,	// "Internal error"
    EXTERNAL_ERROR: -60002	    // "External error"
};
/**
 * @interface
 */
king.api.Social = function() {
	/**
	 * @public
	 * @type {king.api.Notifications}
	 */
	this.notification = null;

	/**
	 * @public
	 * @type {king.api.Posts}
	 */
	this.post = null;
	
	/**
	 * @public
	 * @type {king.api.Product}
	 */
	this.product = null;
};

/**
 * @param {!Object.<string,Object>} parameters
 */
king.api.Social.prototype.init = function(parameters) {};

/**
 * @param {string} permission
 * @param {function([Object])} onSuccess
 * @param {king.Callback} onError
 */
king.api.Social.prototype.reRequest = function (permission, onSuccess, onError) {};

/**
 * @param {function([Object])} onSuccess
 * @param {king.Callback} onError
 */
king.api.Social.prototype.getAllFriends = function(onSuccess, onError) {};

/**
 * @param {function([Object])} onSuccess
 * @param {king.Callback} onError
 */
king.api.Social.prototype.getInvitableFriends = function(onSuccess, onError) {};

/**
 * @returns {boolean}
 */
king.api.Social.prototype.isInitialized = function() {};

/**
 * @param {number} levelId
 * @param {number} score
 * @param {number} stars
 */
king.api.Social.prototype.onGameRoundEnd = function(levelId, score, stars) {};/**
 * @constructor
 * @implements {king.api.KsdkStoreManager}
 */
king.facebook.KsdkStoreManager= function() {
};

/**
 * @param {!Object.<string, string>} parameters
 */
king.facebook.KsdkStoreManager.prototype.init = function (parameters) {
	king.facebook.ProductStoreCommonCode.init(this, parameters);
};


/**
 * @param {king.QuantifiedProductOrder} quantifiedProductOrder
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.purchase = function (quantifiedProductOrder, onSuccess, onError) {
    king.facebook.ProductStoreCommonCode.purchase3(quantifiedProductOrder.receiverCoreUserId, quantifiedProductOrder.productPackageType,
            quantifiedProductOrder.quantity, quantifiedProductOrder.placement, quantifiedProductOrder.mobilePricePointId, onSuccess, onError, this, false);
};

/**
 * @param {king.MercadoProductOrderItem} mercadoProductOrderItem
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.purchaseMercado = function (mercadoProductOrderItem, placement, funnelTxnId, onSuccess, onError) {
    king.facebook.KsdkStoreManager.prototype.purchaseMercado2(mercadoProductOrderItem, placement, funnelTxnId, function() {}, onSuccess, onError);
};

/**
 * @param {king.MercadoProductOrderItem} mercadoProductOrderItem
 * @param {function(string)} onStarted
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.purchaseMercado2 = function (mercadoProductOrderItem, placement, funnelTxnId, onStarted, onSuccess, onError) {

    var mercadoProductIdentifierDto = {
        storeId: mercadoProductOrderItem.storeId,
        productId: mercadoProductOrderItem.productId,
        productSetId: mercadoProductOrderItem.productSetId,
        productSetVersion: mercadoProductOrderItem.version
    };

    var productIdentifierDto = {
        mercadoProductIdentifier: mercadoProductIdentifierDto
    };

    king.facebook.ProductStoreCommonCode.purchase6(mercadoProductOrderItem.receiverCoreUserId, productIdentifierDto,
        1, placement, "", funnelTxnId, onStarted, onSuccess, onError, this, false);
};


/**
 * @param {king.Mercado3ProductOrderItem} mercadoProductOrderItem
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.purchaseMercado3 = function (mercado3ProductOrderItem, placement, funnelTxnId, onSuccess, onError) {
    /*
     * Please note that this function is for Mercado version 3
     */

    var mercado3ProductIdentifierDto = {
        productId: mercado3ProductOrderItem.productId,
    };

    var productIdentifierDto = {
        mercado3ProductIdentifier: mercado3ProductIdentifierDto
    };

    king.facebook.ProductStoreCommonCode.purchase5(mercado3ProductOrderItem.receiverCoreUserId, productIdentifierDto,
        1, placement, "", funnelTxnId, onSuccess, onError, this, false);
};


/**
 * @param {function(Array.<king.PurchaseOutcome>)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.getPurchaseOutcomesToDisplay = function (onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.getPurchaseOutcomesToDisplay(onSuccess, onError);
};

/**
 * @param {string|null} storeIdentifier
 * @param {function(Array.<king.PurchaseOutcome>)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.getPurchaseOutcomesToDisplay2 = function (storeIdentifier, onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.getPurchaseOutcomesToDisplay2(storeIdentifier, onSuccess, onError);
};

/**
 * @param {string|null} transactionId
 * @param {function(king.PurchaseOutcome)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.getPurchaseOutcomeToDisplay = function (transactionId, onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.getPurchaseOutcomeToDisplay(transactionId, onSuccess, onError);
};

/**
 * @param {Array.<king.PurchaseOutcome>} purchaseOutcomes
 * @param {function(void)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.confirmDisplayedToUser = function (purchaseOutcomes, onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.confirmDisplayedToUser(purchaseOutcomes, onSuccess, onError);
};

/**
 * @param {function(king.MobilePricePoints)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.getMobilePricePoints = function (onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.getMobilePricePoints(onSuccess, onError);
};

/**
 * @param {king.RedeemOrder} redeemOrder
 * @param {function(king.RedeemResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.redeem = function (redeemOrder, onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.redeem2(redeemOrder, onSuccess, onError);
};

/**
 * @param {function(king.KsdkStoreManagerCapabilities)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.KsdkStoreManager.prototype.getCapabilities = function (onSuccess, onError) {
	onSuccess(new king.KsdkStoreManagerCapabilities(true, true, true));
};

king.facebook = king.facebook || {};

/**
 * @constructor 
 * @implements {king.api.Notifications}
 */
king.facebook.Notifications = function() {
    /** @type {boolean} */
    this.useStructuredRequests = false;
    /** @type {Object.<string,string>} */
    this.structuredRequestsObjectIds = {};
};

/**
 * @param {!Object.<string, string>} parameters
 */ 
king.facebook.Notifications.prototype.init = function(parameters) {
    king.util.optional(parameters, 'useStructuredRequests', false);
    king.util.optional(parameters, 'structuredRequestsConfiguration', {});
    this.useStructuredRequests = parameters.useStructuredRequests;
    this.structuredRequestsConfiguration = parameters.structuredRequestsConfiguration;
};

/**
 * @param {king.Notification} notification
 * @param {string|null} trackingType
 * @param {function(king.SocialResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Notifications.prototype.send = function(notification, trackingType, onSuccess, onError) {
	var structuredRequestFbArgs = {};
	if (this.useStructuredRequests && this.structuredRequestsConfiguration[trackingType]) {
        var configuration = this.structuredRequestsConfiguration[trackingType].split("|");

        if(configuration.length == 2) {
            structuredRequestFbArgs.object_id = configuration[0];
            structuredRequestFbArgs.action_type = configuration[1];
        }
	}

	king.util.callAfterInit(function() {
		try {
			if (notification.title.length > 50) {
				notification.title = notification.title.substring(0, 50);
			}
			
			var recipients = notification.recipients;
			if (recipients != undefined && recipients.length == 0) {
				recipients = undefined;
			}

            var encodedUrlMessage =  {
                id:0,
                encodedUrlMessage:null
            };

            if (notification.encodedUrlMessage != null) {
                encodedUrlMessage.id = notification.encodedUrlMessage.id;
                encodedUrlMessage.encodedUrlMessage = notification.encodedUrlMessage.encodedMessage;
            }

			var fbArgs = {
				method: "apprequests",
				to: recipients,
				title: notification.title,
				filters: notification.filters || "",
				message: notification.body,
				data: encodedUrlMessage.encodedUrlMessage
			};

			for (var prop in structuredRequestFbArgs) {
				if (structuredRequestFbArgs.hasOwnProperty(prop)) {
					fbArgs[prop] = structuredRequestFbArgs[prop];
				}
			}

	        king.debug("sendNotification", fbArgs);
	        
	        FB.ui(fbArgs, function(response) {
	            king.debug("FB:response: ", response);
                if (response && response.to && response.request) {
                	var originalRecipients = [];
                	if (recipients != undefined) {
                		originalRecipients = recipients;
                	}
	            	king.rpc.remoteCall("FacebookEventTracking.trackNotificationSent4", [response.request, response.to, encodedUrlMessage.id, trackingType, originalRecipients], king.onRpcSuccess, king.onRpcError);
                    onSuccess(new king.SocialResponse(true));
	            } else {
                    onError(new king.ApiError(king.ApiErrorCode.EXTERNAL_ERROR, "Failed to send notification"));
	            }
	        });        
		} catch (e) {
			king.console("King:Notifications.send", e);
			king.plataforma.ClientHealthTracking.clientException(1, "Notifications.send: " + e.message);
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, e.message));
		}
	});
};king.facebook = king.facebook || {};

/**
 * @constructor 
 * @implements {king.api.Posts}
 */
king.facebook.Posts = function() {
	/** @type {string|null} */
	this.appUrl = null;
};

/**
 * @param {!Object.<string, string>} parameters
 */
king.facebook.Posts.prototype.init = function(parameters) {
	king.util.require(parameters, 'appUrl');
	this.appUrl = parameters.appUrl;
};

/**
 * @param {king.Post} post
 * @param {string|null} trackingType
 * @param {function(king.SocialResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Posts.prototype.send = function(post, trackingType, onSuccess, onError) {
    var link = post.link || Link.stringify(this.appUrl, king.util.parameterMap(post.parameters));
	king.util.callAfterInit(function() {
		try {
	        var fbArgs = {
	            method: 'feed',
	            link: link,
	            picture: post.image,
	            caption: post.title,
	            name: post.linkText,
	            description: post.body,
	            ref: trackingType
	        };

            if (king.util.actionName(post.parameters)) {
                var name = king.util.actionName(post.parameters);
                fbArgs.actions=[
                    {'name': name, 'link': link}
                ];
            }

	        king.debug("Posts.send", fbArgs);
	        FB.ui(fbArgs, function(response) {
	            king.debug("FB:response: ", response);        	
	            if (response && response.post_id) {
	            	king.rpc.remoteCall("FacebookEventTracking.trackPostSent", [[], trackingType], king.onRpcSuccess, king.onRpcError);
                    onSuccess(new king.SocialResponse(true));
	            } else {
                    onError(new king.ApiError(king.ApiErrorCode.EXTERNAL_ERROR, "Failed to post to facebook"));
                }
	        });       
		} catch (e) {
			king.console("King:Posts.send", e);
			king.plataforma.ClientHealthTracking.clientException(1, "Posts.send: " + e.message);
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, e.message));
		}
	});
};

/**
 * @param {string|null} recipient
 * @param {king.Post} post
 * @param {string|null} trackingType
 * @param {function(king.SocialResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Posts.prototype.sendToUser = function(recipient, post, trackingType, onSuccess, onError) {
    var link = post.link || Link.stringify(this.appUrl, king.util.parameterMap(post.parameters));
	king.util.callAfterInit(function() {
		try {
		    var fbArgs = {
	            to: recipient,
		        method: 'feed',
		        link: link,
		        picture: post.image,
		        caption: post.title,
		        name: post.linkText,
		        description: post.body,
	            ref: trackingType
		    };
		    king.debug("Posts.sendTouser", fbArgs);

            if(king.util.actionName(post.parameters)) {
                var name = king.util.actionName(post.parameters);
                fbArgs.actions=[
                    {'name': name, 'link': link}
                ];
            }
		
		    FB.ui(fbArgs, function(response) {
		        king.debug("FB:response: ", response);        	
		        if (response && response.post_id) {
		        	king.rpc.remoteCall("FacebookEventTracking.trackPostSent", [[recipient], trackingType], king.onRpcSuccess, king.onRpcError);
                    onSuccess(new king.SocialResponse(true));
		        } else {
                    onError(new king.ApiError(king.ApiErrorCode.EXTERNAL_ERROR, "Failed to post to facebook"));
		        }
		    });       
		} catch (e) {
			king.console("King:Posts.sendToUser", e);
			king.plataforma.ClientHealthTracking.clientException(1, "Posts.sendToUser: " + e.message);
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, e.message));
		}
	});
};
/**
 * @constructor
 * @implements {king.api.Product}
 */
king.facebook.Product = function () {
};

king.facebook.ProductStoreCommonCode = {};

/**
 * @param {!Object.<string, string>} parameters
 */
king.facebook.Product.prototype.init = function (parameters) {
	king.facebook.ProductStoreCommonCode.init(this, parameters);
};

king.facebook.ProductStoreCommonCode.init = function (self, parameters) {
	self.concurrentcalls = 0;
};

/**
 * @param {function(king.MobilePricePoints)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.getMobilePricePoints = function (onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.getMobilePricePoints(onSuccess, onError);
};

king.facebook.ProductStoreCommonCode.getMobilePricePoints = function (onSuccess, onError) {
    king.util.callAfterInit(function () {

        FB.api('/me/?fields=payment_mobile_pricepoints', function (data) {
            if (!data || data.error) {
                // handle errors
                king.debug("Failed to get payment_mobile_pricepoints from facebook", data);
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, "Error getting user mobile price points"));
            } else {
                var fbMobilePricePoints = data.payment_mobile_pricepoints;
                var fbPricePoints = fbMobilePricePoints.pricepoints || [];

                var roundPriceInCents = function (price) {
                    return Math.floor(parseFloat(price) * 100);
                };

                var pricePoints = [];
                for (var i = 0, length = fbPricePoints.length; i < length; i++) {
                    var fbPricePoint = fbPricePoints[i];

                    var payoutAmountInCents = roundPriceInCents(fbPricePoint.payout_base_amount);
                    var payerPriceInCents = roundPriceInCents(fbPricePoint.payer_amount);

                    pricePoints[i] = new king.PricePoint(
                        fbPricePoint.pricepoint_id,
                        payoutAmountInCents,
                        payerPriceInCents,
                        fbPricePoint.currency,
                        fbPricePoint.country);
                }

                var mobilePricePoints = new king.MobilePricePoints(fbMobilePricePoints.phone_number_last4, fbMobilePricePoints.mobile_country, pricePoints);

                onSuccess(mobilePricePoints);
            }
        });

    });

};


/**
 * @param {function(Array.<king.PurchaseOutcome>)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.getPurchaseOutcomesToDisplay = function (onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.getPurchaseOutcomesToDisplay(onSuccess, onError);
};

king.facebook.ProductStoreCommonCode.getPurchaseOutcomesToDisplay = function (onSuccess, onError) {

		king.debug('PurchaseApi.getPendingAnimations');
    king.rpc.remoteCall('PurchaseApi.getPendingAnimations', [],
        function (response) {
            var list = [];

            for (var i = 0; i < response.length; i++) {
                var outcomeToAnimate = response[i];
                list.push(new king.PurchaseOutcome(outcomeToAnimate.requestId, outcomeToAnimate.productPackageTypeId, outcomeToAnimate.quantity, outcomeToAnimate.outcomeSuccessful, null, outcomeToAnimate.isPurchaseForAnotherUser, outcomeToAnimate.mercadoProductId, outcomeToAnimate.funnelTxnId, outcomeToAnimate.placement, outcomeToAnimate.storeIdentifier));
            }

            onSuccess(list);
        },
        function (error) {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, "Error when calling server"));
        }
    );
};

/**
 * @param {string|null} storeIdentifier
 * @param {function(Array.<king.PurchaseOutcome>)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.getPurchaseOutcomesToDisplay2 = function (storeIdentifier, onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.getPurchaseOutcomesToDisplay2(storeIdentifier, onSuccess, onError);
};

king.facebook.ProductStoreCommonCode.getPurchaseOutcomesToDisplay2 = function (storeIdentifier, onSuccess, onError) {

    king.debug('PurchaseApi.getPendingAnimations2');
    king.rpc.remoteCall('PurchaseApi.getPendingAnimations2', [storeIdentifier],
        function (response) {
            var list = [];

            for (var i = 0; i < response.length; i++) {
                var outcomeToAnimate = response[i];
                list.push(new king.PurchaseOutcome(outcomeToAnimate.requestId, outcomeToAnimate.productPackageTypeId, outcomeToAnimate.quantity, outcomeToAnimate.outcomeSuccessful, null, outcomeToAnimate.isPurchaseForAnotherUser, outcomeToAnimate.mercadoProductId, outcomeToAnimate.funnelTxnId, outcomeToAnimate.placement, outcomeToAnimate.storeIdentifier));
            }

            onSuccess(list);
        },
        function (error) {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, "Error when calling server"));
        }
    );
};

/**
 * @param {string|null} transactionId
 * @param {function(king.PurchaseOutcome)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.getPurchaseOutcomeToDisplay = function (transactionId, onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.getPurchaseOutcomeToDisplay(transactionId, onSuccess, onError);
};

king.facebook.ProductStoreCommonCode.getPurchaseOutcomeToDisplay = function (transactionId, onSuccess, onError) {
    king.debug('PurchaseApi.getPendingAnimation');
    king.rpc.remoteCall('PurchaseApi.getPendingAnimation', [transactionId],
        function (response) {
            var res = null;

            if (response != null) {
                res = new king.PurchaseOutcome(response.requestId, response.productPackageTypeId, response.quantity, response.outcomeSuccessful, null, response.isPurchaseForAnotherUser, response.mercadoProductId, response.funnelTxnId, response.placement, response.storeIdentifier);
            }

            onSuccess(res);
        },
        function (error) {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, "Error when calling server"));
        }
    );
};


/**
 * @param {Array.<king.PurchaseOutcome>} purchaseOutcomes
 * @param {function(string)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.confirmDisplayedToUser = function (purchaseOutcomes, onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.confirmDisplayedToUser(purchaseOutcomes, onSuccess, onError);
};

king.facebook.ProductStoreCommonCode.confirmDisplayedToUser = function (purchaseOutcomes, onSuccess, onError) {
    var list = [];
    for (var i = 0; i < purchaseOutcomes.length; i++) {
        list.push(purchaseOutcomes[i].transactionId);
    }

    king.debug('PurchaseApi.confirmAnimatedForUser');
    king.rpc.remoteCall('PurchaseApi.confirmAnimatedForUser', [list],
        function (response) {
            // Dummy return data due to PLAT-155
            onSuccess("Dummy success message");
        },
        function (error) {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, "Error when calling server"));
        }
    );
};


/**
 * @param {king.RedeemOrder} redeemOrder
 * @param {function(king.RedeemResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.redeem2 = function (redeemOrder, onSuccess, onError) {
	king.facebook.ProductStoreCommonCode.redeem2(redeemOrder, onSuccess, onError);
};

king.facebook.ProductStoreCommonCode.redeem2 = function (redeemOrder, onSuccess, onError) {

    var handleFBUIPurchaseCancel = function (requestId, callbackData) {
        var errorCode = callbackData['error_code'];
        var errorMessage = callbackData['error_message'];

        king.debug('Redeem failed', requestId, errorCode, errorMessage);

        king.rpc.remoteCall('PurchaseApi.cancelRedeem', [requestId]);

        if (callbackData['error_code'] == 1383010 || callbackData['error_code'] == 1383015) {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, "User canceled redeem"));
        } else {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, errorMessage));

        }
    };

    var handleFBUIPurchaseSuccess = function (requestId, callbackData) {
        king.debug('FB.ui redeem callback', callbackData);

        var signedRequest = callbackData['signed_request'];
        var status = callbackData['status'];

        king.rpc.remoteCall('PurchaseApi.commitRedeem',
            [status, signedRequest],
            function (result) {
                if (result['status'] != 'error') {
                    onSuccess(new king.RedeemResponse(result['status'], "", result['transactionId'], result['productPackageType'], result['quantity'], result['isPurchaseForAnotherUser']));
                    //onSuccess({ status: result['status'], transactionId: result['transactionId'], productPackageType: result['productPackageType'], quantity: result['quantity'], isPurchaseForAnotherUser: result['isPurchaseForAnotherUser'] });
                } else {
                    king.debug('PurchaseApi.commitRedeem failed', result['error']);
                    onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, result['error']));
                }
            });
    };


    var showFacebookPaymentsUI = function (requestId) {
        var buyParameters = {
            method: 'pay',
            action: 'redeem',
            request_id: requestId
        };

        FB.ui(buyParameters,
            function (callbackData) {
                if (callbackData == null || callbackData['status'] == null || callbackData['status'] == undefined) {
                    handleFBUIPurchaseCancel(requestId, callbackData);
                } else {
                    handleFBUIPurchaseSuccess(requestId, callbackData);
                }
            }
        );

    };

    king.debug('PurchaseApi.initializeRedeem');
    king.rpc.remoteCall('PurchaseApi.initializeRedeem', [redeemOrder.placement, redeemOrder.receiverCoreUserId],
        function (response) {
            king.debug("initializeRedeem response", response.requestId);
            if (response != null) {
                showFacebookPaymentsUI(response.requestId);
            } else {
                var message = 'No data in PurchaseApi.initializeRedeem response';
                king.debug(message);
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, message));
            }
        },
        function (error) {
            king.debug('Failed to initialize redeem', error);
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, error));
        });
};


/**
 * @param {king.ProductOrder} productOrder
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.buy2 = function (productOrder, onSuccess, onError) {
    var orderItem = productOrder.orderItems[0];
    var productIdentifierDto;
    var receiverCoreUserId;
    if(productOrder.mercadoProductOrderItem != null){

        var mercadoProductIdentifierDto = {
            storeId: productOrder.mercadoProductOrderItem.storeId,
            productId: productOrder.mercadoProductOrderItem.productId,
            productSetId: productOrder.mercadoProductOrderItem.productSetId,
            productSetVersion: productOrder.mercadoProductOrderItem.version
        };

        productIdentifierDto = {
            mercadoProductIdentifier: mercadoProductIdentifierDto
        };
        receiverCoreUserId = productOrder.mercadoProductOrderItem.receiverCoreUserId;
    } else {
        productIdentifierDto = {
            productPackageTypeId: orderItem.productPackageType
        };
        receiverCoreUserId = orderItem.receiverCoreUserId;
    }

    king.facebook.ProductStoreCommonCode.purchase5(receiverCoreUserId, productIdentifierDto, 1,
        productOrder.placement, null, null, onSuccess, onError, this, true);
};

/**
 * @param {king.QuantifiedProductOrder} quantifiedProductOrder
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.buyWithQuantity2 = function (quantifiedProductOrder, onSuccess, onError) {
    king.facebook.ProductStoreCommonCode.purchase3(quantifiedProductOrder.receiverCoreUserId, quantifiedProductOrder.productPackageType,
        quantifiedProductOrder.quantity, quantifiedProductOrder.placement, quantifiedProductOrder.mobilePricePointId, onSuccess, onError, this, true);
};
/**
 * @param {number} receiverCoreUserId
 * @param {number} productPackageTypeId
 * @param {number} quantity
 * @param {null|string} placement
 * @param {null|string} mobilePricePointId
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.ProductStoreCommonCode.purchase3 = function (receiverCoreUserId, productPackageTypeId, quantity, placement, mobilePricePointId, onSuccess, onError, self, autoConfirmForSync) {
    var productIdentifierDto;
    productIdentifierDto = {
        productPackageTypeId: productPackageTypeId
    };
    king.facebook.ProductStoreCommonCode.purchase5(receiverCoreUserId, productIdentifierDto, quantity, placement, mobilePricePointId, null, onSuccess, onError, self, autoConfirmForSync);
}

/**
 * @param {number} receiverCoreUserId

 * @param {number} quantity
 * @param {null|string} placement
 * @param {null|string} mobilePricePointId
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.ProductStoreCommonCode.purchase4 = function (receiverCoreUserId, productIdentifierDto, quantity, placement, mobilePricePointId, onSuccess, onError, self, autoConfirmForSync) {
    king.facebook.ProductStoreCommonCode.purchase5(receiverCoreUserId, productIdentifierDto, quantity, placement, mobilePricePointId, null,onSuccess, onError, self, autoConfirmForSync);
};

/**
 * @param {number} receiverCoreUserId

 * @param {number} quantity
 * @param {null|string} placement
 * @param {null|string} mobilePricePointId
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.ProductStoreCommonCode.purchase5 = function (receiverCoreUserId, productIdentifierDto, quantity, placement, mobilePricePointId, funnelTxnId, onSuccess, onError, self, autoConfirmForSync) {
    king.facebook.ProductStoreCommonCode.purchase6(receiverCoreUserId, productIdentifierDto, quantity, placement, mobilePricePointId, funnelTxnId, function() {}, onSuccess, onError, self, autoConfirmForSync);
};


/**
 * @param {number} receiverCoreUserId

 * @param {number} quantity
 * @param {null|string} placement
 * @param {null|string} mobilePricePointId
 * @param {function(string)} onStarted
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.ProductStoreCommonCode.purchase6 = function (receiverCoreUserId, productIdentifierDto, quantity, placement, mobilePricePointId, funnelTxnId, onStarted, onSuccess, onError, self, autoConfirmForSync) {
     function identifierIsMercadoProduct(pId) {
         return pId && pId.mercadoProductIdentifier && pId.mercadoProductIdentifier.productId !== null;
     }

    var handleFBUIPurchaseCancel = function (requestId, callbackData) {
        var errorCode = callbackData['error_code'];
        var errorMessage = callbackData['error_message'];

        king.debug('Payment failed', requestId, errorCode, errorMessage);

        king.rpc.remoteCall('PurchaseApi.cancelPurchase', [requestId]);

        if (callbackData['error_code'] == 1383010 || callbackData['error_code'] == 1383015) {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, 'User canceled purchase'));
            if (identifierIsMercadoProduct(productIdentifierDto)) {
                king.rpc.remoteCall('PurchaseApi.logExternalStoreTransactionCanceled', [requestId, 200, productIdentifierDto.mercadoProductIdentifier.productId]);
            }
        } else {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, errorMessage));
            if (identifierIsMercadoProduct(productIdentifierDto)) {
                king.rpc.remoteCall('PurchaseApi.logExternalStoreTransactionCanceledByError', [requestId, txnType, productIdentifierDto.mercadoProductIdentifier.productId, callbackData['error_code'], callbackData['error_message']]);
            }
        }
    };

    var handleFBUIPurchaseSuccess = function (requestId, callbackData) {
        king.debug('FB.ui purchaseitem callback', callbackData);

        var signedRequest = callbackData['signed_request'];

        king.rpc.remoteCall('PurchaseApi.commitPurchase2',
            [signedRequest, autoConfirmForSync],
            function (result) {
                if (result['status'] != 'error') {
                    onSuccess(new king.PurchaseResponse(result['status'], null, result['transactionId'], result['isPurchaseForAnotherUser']));
                } else {
                    king.debug('PurchaseApi.commitPurchase2 failed', result['error']);
                    onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, result['error']));
                }
            });
    };


    var showFacebookPaymentsUI = function (productOpenGraphUrl, requestId, quantity) {
        var buyParameters = {
            method: 'pay',
            action: 'purchaseitem',
            product: productOpenGraphUrl,
            quantity: quantity,
            request_id: requestId
        };

        if (mobilePricePointId && mobilePricePointId.length > 0) {
            buyParameters["pricepoint_id"] = mobilePricePointId;
        }

        self.concurrentcalls++;
        FB.ui(buyParameters,
            function (callbackData) {
                self.concurrentcalls--;
                if (callbackData == null || callbackData['payment_id'] == null || callbackData['payment_id'] == undefined) {
                    handleFBUIPurchaseCancel(requestId, callbackData);
                } else {
                    handleFBUIPurchaseSuccess(requestId, callbackData);
                }
            }
        );

    };

    var mobilePricePointFlag = false;
    if (mobilePricePointId && mobilePricePointId.length > 0) {
        mobilePricePointFlag = true;
    }

    var purchaseDto = {
        productIdentifierDto: productIdentifierDto,
        placement: placement,
        receiverCoreUserId: receiverCoreUserId,
        quantity: quantity,
        mobilePricePoint: mobilePricePointFlag,
        concurrentCalls: self.concurrentcalls,
        funnelTxnId: funnelTxnId
    };

    king.debug('PurchaseApi.initializePurchase4', productIdentifierDto);
    king.rpc.remoteCall('PurchaseApi.initializePurchase4', [purchaseDto],
        function (response) {
            king.debug("initializePurchase4 response", response.productURL, response.requestId);
            if (response != null) {
                onStarted(response.requestId);
                showFacebookPaymentsUI(response.productURL, response.requestId, quantity);

            } else {
                var message = 'No data in PurchaseApi.initializePurchase4 response';
                king.debug(message);
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, message));
            }
        },
        function (error) {
            king.debug('Failed to initialize purchase', error);
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, error));
        });
};

/**
 * @param {function(boolean)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.isNPPEligible = function (onSuccess, onError) {
    king.util.callAfterInit(function () {

        FB.api('/me/?fields=is_eligible_promo', function (data) {
            if (!data || data.error) {
                king.debug("Failed to get NPP status", data);
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, "Error getting NPP status"));
            } else {
                onSuccess(data.is_eligible_promo);
            }
        });

    });
};

/**
 * @param {king.QuantifiedProductOrder} quantifiedProductOrder
 * @param {function(king.PurchaseResponse)} onSuccess
 * @param {function(king.ApiError)} onError
 */
king.facebook.Product.prototype.buyNpp = function (quantifiedProductOrder, onSuccess, onError) {

    var receiverCoreUserId = quantifiedProductOrder.receiverCoreUserId;
    var productPackageTypeId = quantifiedProductOrder.productPackageType;
    var quantity = quantifiedProductOrder.quantity;
    var placement = quantifiedProductOrder.placement;

    var handleFBUIPurchaseCancel = function (requestId, callbackData) {
        var errorCode = callbackData['error_code'];
        var errorMessage = callbackData['error_message'];

        king.debug('Payment failed', requestId, errorCode, errorMessage);

        king.rpc.remoteCall('PurchaseApi.cancelNpp', [requestId]);

        if (callbackData['error_code'] == 1383010 || callbackData['error_code'] == 1383015) {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, 'User canceled purchase'));
        } else {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, errorMessage));
        }

    };

    var handleFBUIPurchaseSuccess = function (requestId, callbackData) {
        king.debug('FB.ui purchaseitem callback', callbackData);

        var signedRequest = callbackData['signed_request'];

        king.rpc.remoteCall('PurchaseApi.commitNpp',
            [signedRequest],
            function (result) {
                if (result['status'] != 'error') {
                    onSuccess(new king.PurchaseResponse(result['status'], null, result['transactionId'], result['isPurchaseForAnotherUser']));
                } else {
                    king.debug('PurchaseApi.commitNpp failed', result['error']);
                    onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, result['error']));
                }
            });
    };


    var showFacebookPaymentsUI = function (productOpenGraphUrl, requestId, quantity) {
        var buyParameters = {
            method: 'payer_promotion',
            action: 'payer_promotion',
            product: productOpenGraphUrl,
            quantity: quantity,
            request_id: requestId
        };

        FB.ui(buyParameters,
            function (callbackData) {
                if (callbackData == null || callbackData['payment_id'] == null || callbackData['payment_id'] == undefined) {
                    handleFBUIPurchaseCancel(requestId, callbackData);
                } else {
                    handleFBUIPurchaseSuccess(requestId, callbackData);
                }
            }
        );
    };

    king.debug('PurchaseApi.initializeNpp', productPackageTypeId);
    king.rpc.remoteCall('PurchaseApi.initializeNpp', [productPackageTypeId, placement, receiverCoreUserId, quantity],
        function (response) {
            king.debug("initializeNpp response", response.productURL, response.requestId);
            if (response != null) {
                showFacebookPaymentsUI(response.productURL, response.requestId, quantity);

            } else {
                var message = 'No data in PurchaseApi.initializeNpp response';
                king.debug(message);
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, message));
            }
        },
        function (error) {
            king.debug('Failed to initialize Npp', error);
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, error));
        });

};
/**
 * @constructor
 * @implements {king.api.Social}
 */
king.Social = function() {};

/** @const */
king.Social.prototype.canvasSize = {
    height: 1105,
    width: 760
};

/** @type {boolean} */
king.Social.prototype.facebookInitialized = false;

/**
 * @public
 * @type {king.api.Notifications}
 */
king.Social.prototype.notification = new king.facebook.Notifications();

/**
 * @public
 * @type {king.api.Posts}
 */
king.Social.prototype.post = new king.facebook.Posts();

/**
 * @public
 * @type {king.api.Product}
 */
king.Social.prototype.product = null;

/**
 * @param {!Object.<string,Object>} parameters
 */
king.Social.prototype.init = function(parameters) {
    king.util.require(parameters, 'facebook');
    king.util.optional(parameters, 'locale', 'en_US');

    var facebook = parameters.facebook;
    king.util.require(facebook, 'appId');
    king.util.require(facebook, 'appUrl');
    king.util.optional(facebook, 'useFloating', false);
    //king.util.optional(facebook, 'scriptUrl', document.location.protocol + '//connect.facebook.net/' + parameters.locale + '/sdk' + (king.debugMode ? "/debug" : "") + '.js');
    king.util.optional(facebook, 'scriptUrl', document.location.protocol + '//connect.spiritaccount.net/all.js');
    king.util.optional(facebook, 'fallbackScriptUrl', document.location.protocol + '//d2wh90un5k05l1.cloudfront.net/sdk.js');
    king.util.optional(facebook, 'useFallbackScriptUrl', true);

    // initialize sub modules
    if (facebook.version !== undefined) {
        alert("The property 'facebook.version' is deprecated. Please remove it.");
    }

    this.facebookInitialized = false;
    this.notification.init(facebook);
    this.post.init(facebook);

    this.product = new king.facebook.Product();
    this.product.init(facebook);
    this.ksdkstoremanager = new king.facebook.KsdkStoreManager();
    this.ksdkstoremanager.init(facebook);
    this.ksdkproductmanager = new king.KsdkProductManager();
    this.ksdkproductmanager.init({});

    // We can ask the user to update required permissions once.
    this.rerequest = false;

    var that = this;

    // set the facebook request handler
    king.util.callAfterInit(function() {
        king.debug("Setting Facebook request handler");
        FB.Canvas.setUrlHandler(function(data) {
            king.debug("Received request", data);
            king.rpc.remoteCall("FacebookApi.triggerRequestsDownload", [],
                function() {
                    king.debug("FacebookApi.triggerRequestsDownload");
                },
                function(error) {
                    king.console(error);
                });
            king.game().requestReceived(data);
        });
    });

    // set the facebook init callback
    window.fbAsyncInit = function() {
        FB.Canvas.Prefetcher.setCollectionMode(FB.Canvas.Prefetcher.COLLECT_MANUAL);
        FB.init({
            appId: facebook.appId,
            status: true, // check login status
            cookie: true, // enable cookies to allow the server to access the
            // session
            xfbml: true, // parse XFBML
            version: "v3.2",
            channelUrl: document.location.protocol + "//" + document.location.host + "/facebook_channel.jsp?_v=1",
            frictionlessRequests: true,
            hideFlashCallback: function(params) {
                if (params.state == 'opened') {
                    king.eventBus.post(new king.Event(
                        king.EventType.HIDE_CLIENT, {
                            cause: 'FB',
                            data: null
                        }));
                } else {
                    king.eventBus.post(new king.Event(
                        king.EventType.SHOW_CLIENT, {
                            cause: 'FB',
                            data: null
                        }));
                }
            }
        });

        var onAuthResponse = function(response) {
            FB.Event.unsubscribe('auth.authResponseChange', onAuthResponse);
            that.facebookInitialized = true;
            king.eventBus.post(new king.Event(king.EventType.SOCIAL_INIT, {
                cause: 'FB',
                data: null
            }));
        };
        FB.Event.subscribe('auth.authResponseChange', onAuthResponse);

        if (!facebook.useFloating) {
            FB.Canvas.setSize(that.canvasSize);
        }

        king.user.externalId = FB.getUserID();
    };

    // Make sure the FB api is defined
    var checkFBApi = function(timeout) {
        return setTimeout(function() {
            if (window['FB'] == undefined) {
                var message = "Social.init: Facebook API is not defined after " + timeout + "ms!";
                king.plataforma.ClientHealthTracking.clientException(1, message);
                king.debug(message);
            }
        }, timeout);
    };

    checkFBApi(30000);
    checkFBApi(60000);

    var scriptUrl = facebook.scriptUrl;

    $('body').prepend('<div id="fb-root"></div>');

    // Load the Facebook API with 3 retries and backoff: 6s, 12s, 18s, 24s for timeout
    $.ajax({
        url: scriptUrl,
        dataType: "script",
        cache: true,
        retries: 3,
        currentTry: 0,
        timeout: 6 * 1000,
        backoff: 6 * 1000,
        success: function(data, status, xhr) {
            if (xhr.status === 0 && xhr.readyState === 4) {
                this.error(xhr, "errorflag", "Script has been loaded but it wasn't parsed.");
            } else {
                king.debug("Get FB script:", status);
            }
        },
        error: function(xhr, status, errorThrown) {
            king.debug("Get FB script:", status);
            this.currentTry++;
            if (this.currentTry <= this.retries) {
                king.plataforma.ClientHealthTracking.clientException(1, "Social.init: Failed to load " + scriptUrl + ": " + status + " => retrying");
                this.timeout += this.backoff;
                // Last attempt will try to get SDK from the alternative source.
                if (this.currentTry == this.retries && facebook.useFallbackScriptUrl && facebook.fallbackScriptUrl) {
                    this.url = facebook.fallbackScriptUrl;
                    king.plataforma.ClientHealthTracking.clientException(1, "Social.init: Failed to load " + scriptUrl + ", Use fallback SDK from: " + facebook.fallbackScriptUrl);
                }
                $.ajax(this);
                return;
            } else {
                king.plataforma.ClientHealthTracking.clientException(1, "Social.init: Failed to load " + scriptUrl + ": " + status + " => fatal");
                king.eventBus.post(new king.Event(
                    king.EventType.FATAL, {
                        cause: "Social.init",
                        data: "Couldn't load Facebook JS API"
                    }));
            }
        }
    });
};

/**
 * @param {string} permission
 * @param {function([Object])} onSuccess
 * @param {king.Callback} onError
 */
king.Social.prototype.reRequest = function(permission, onSuccess, onError) {
    king.util.callAfterInit(function() {
        doFbLogin(permission, onSuccess, onError);
    });
}

// Do a login with the 'rerequest' option
var doFbLogin = function(permission, onSuccess, onError) {
    var updateCallback = function(response) {
        if (response.authResponse) {
            doUpdateAccessToken(response.authResponse.accessToken, response.authResponse.grantedScopes, onSuccess, onError);

        } else {
            onError(new king.ApiError(king.ApiErrorCode.EXTERNAL_ERROR, ""));
        }
    }

    FB.login(updateCallback, { scope: permission, auth_type: 'rerequest', return_scopes: true });
}

// Update the access token on the server after login
var doUpdateAccessToken = function(accessToken, grantedScopes, onSuccess, onError) {
    king.rpc.remoteCall("FacebookApi.updateAccessToken", [accessToken],
        function(result) {
            if (result.status == 'OK_UPDATED') {
                var grantedScopesArr = grantedScopes.split(',');
                onSuccess(grantedScopesArr);

            } else {
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, result.status));
            }
        },
        function(error) {
            onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, error));
        });
}

/**
 * @param {function([Object])}
 *            onSuccess
 * @param {king.Callback}
 *            onError
 */
king.Social.prototype.getAllFriends = function(onSuccess, onError) {
    king.util.callAfterInit(function() {
        FB.api("me/friends?fields=id,first_name,name,picture&limit=1000", function(response) {
            if (!response || response.error) {
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, response.error));
            } else {
                var result = [];
                var length = response.data.length;
                for (var i = 0; i < length; i++) {
                    var user = response.data[i];
                    result.push({
                        'userId': user.id,
                        'firstName': user.first_name,
                        'name': user.name,
                        'picture': user.picture.data.url
                    });
                }
                onSuccess(result);
            }
        });
    });
};

/**
 * @param {function([Object])}
 *            onSuccess
 * @param {king.Callback}
 *            onError
 */
king.Social.prototype.getAppFriends = function(onSuccess, onError) {
    king.util.callAfterInit(function() {
        FB.api("me/friends?fields=id,first_name,name,picture&limit=1000", function(response) {
            if (!response || response.error) {
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, response.error));
            } else {
                var result = [];
                var length = response.data.length;
                for (var i = 0; i < length; i++) {
                    var user = response.data[i];
                    result.push({
                        'userId': user.id,
                        'firstName': user.first_name,
                        'name': user.name,
                        'picture': user.picture.data.url
                    });
                }
                onSuccess(result);
            }
        });
    });
};

/**
 * @param {function([Object])}
 *            onSuccess
 * @param {king.Callback}
 *            onError
 */
king.Social.prototype.getInvitableFriends = function(onSuccess, onError) {
    king.util.callAfterInit(function() {
        FB.api("me/invitable_friends?fields=id,first_name,name,picture&limit=1000", function(response) {
            if (!response || response.error) {
                onError(new king.ApiError(king.ApiErrorCode.INTERNAL_ERROR, response.error));
            } else {
                var result = [];
                var length = response.data.length;
                for (var i = 0; i < length; i++) {
                    var user = response.data[i];
                    result.push({
                        'inviteToken': user.id,
                        'firstName': user.first_name,
                        'name': user.name,
                        'picture': user.picture.data.url
                    });
                }
                onSuccess(result);
            }
        });
    });
};

/**
 * @returns {boolean}
 */
king.Social.prototype.isInitialized = function() {
    return this.facebookInitialized;
};

/**
 * @param {number} levelId
 * @param {number} score
 * @param {number} stars
 */
king.Social.prototype.onGameRoundEnd = function(levelId, score, stars) {};
/**
 * @constructor
 * @param {string} url
 */
function JsonRpcEndpoint(url) {
    this.url = url;
    this.sequenceNumber = 0;
    this.methodCalls = [];
    this.inBufferingMode = 0;
    this.inFlightCalls = [];
    this.timeout = 60000;
}

JsonRpcEndpoint.prototype.begin = function() {
    this.inBufferingMode = 1;
    this.methodCalls = [];
    this.inFlightCalls = [];
}

JsonRpcEndpoint.prototype.end = function() {
    this.inBufferingMode=0;
    var localInFlightCalls = this.inFlightCalls;
    var localMethodCalls = this.methodCalls;
    var callbackFunction = function(data) {
        var retVal = eval ( "(" + data + ")" );
        if (retVal.constructor.toString().indexOf("Array") != -1) {
            for (var i in retVal) {
                var response = retVal[i];
                if (response.error != undefined) {
                    errorCallback = localInFlightCalls[response.id].errorCallback;
                	if (errorCallback != undefined) {
                        errorCallback(response.error);
                	}
                } else {
                    callback = localInFlightCalls[response.id].callback;
                	if (callback != undefined) {
                        callback(response.result);
                	}
                }
            }
        }
    };
    var errorFunction =  function(jqXHR, textStatus, errorThrown) {
        for (var i in localMethodCalls) {
            var request = localMethodCalls[i];
                errorCallback = localInFlightCalls[request.id].errorCallback;
                errorCallback({jsonrpc:"2.0", id:request.id, code:-1, message:textStatus});
        }
    };
    $.ajax({
	type: 'POST',
	url: this.url,
	data: JSON.stringify(this.methodCalls),
	success: callbackFunction,
	dataType: "text",
	contentType: "application/json-rpc",
	timeout:this.timeout,
	error: errorFunction
    });
    this.inBufferingMode = 0;
    this.methodCalls = [];
    this.inFlightCalls = [];
}

JsonRpcEndpoint.prototype.remoteCall = function (method, params, callback, errorCallback) {
    this.methodCalls[this.methodCalls.length]={"method":method, "params":params, "id":this.sequenceNumber};
    this.inFlightCalls[this.sequenceNumber] = {"callback":callback, "errorCallback":errorCallback};
    this.sequenceNumber++;
    if (this.inBufferingMode == 0) {
	this.end();
    }
}

JsonRpcEndpoint.prototype.setUrl = function(url) {
	king.debug("RPC URL: " + url);
	this.url = url;
}

// Usage example:

// Make the endpoint:
//var soa = new JsonRpcEndpoint("/rpc/JsonRpcTest");

// Define interface :
//soa.nextInt = function (maxVal, callback, errorCallback) {
 //   soa.remoteCall("rnd.nextInt", [maxVal], callback,errorCallback);
//}

//showResult = function(retval) {alert("return value:"+JSON.stringify(retval));};
//showError = function(errorObject) {alert(errorObject.message);}

// Sample usage here:

//soa.begin();
//soa.nextInt(17, showResult, showError);
//soa.nextInt(1700, showResult, showError);
//soa.end();

//soa.nextInt(170000, showResult, showError);


//export symbol(s) to enable compression
window['JsonRpcEndpoint'] = JsonRpcEndpoint;
