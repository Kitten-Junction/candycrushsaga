var View = function() {
	var view = document.getElementById('view');
	var viewBackground = document.getElementById('view-background');
	var statusElement = document.getElementById('prelaoder-status');
	var progressElement = document.getElementById('prelaoder-progress');

	var mLastScreen = "";
	var mScreens = {};

	function addScreen(name, domObject) {
		mScreens[name] = domObject;
		domObject.style.display = "none";
	}

	function switchToScreen(screen) {
		if (screen == mLastScreen) {
			return;
		}
		mLastScreen = screen;
		for (var screenId in mScreens) {
			if (screenId == screen) {
				mScreens[screenId].style.display = "inherit";
			} else {
				mScreens[screenId].style.display = "none";
			}
		}
		viewBackground.style.display = screen == "game" ? "none" : "inherit";
	}

	addScreen("game", document.getElementById('view-game'));
	addScreen("loading", document.getElementById('view-preloader'));
	addScreen("mobile", document.getElementById('view-mobile'));
	addScreen("incompatibility", document.getElementById('view-incompatibility'));
	addScreen("no-webgl", document.getElementById('view-no-webgl'));
	addScreen("fatal", document.getElementById('view-fatal'));
	addScreen("memory-error", document.getElementById('view-memory-error'));

	var Init = function (config) {
		SetViewSize(config.width, config.height);
	};

	var SetViewSize = function (width, height) {
		view.style.width = width + "px";
		view.style.height = height + "px";
	};

	// Could be used as a fallback to flash.
	var OnBrowserIncompatible = function() {
		switchToScreen("incompatibility");
	};

	// If game has a native version then on this state a link should be displayed
	var OnMobileBrowser = function(mobilePlatform) {
		var platforms = {
			ios : document.getElementById("store-ios"),
			wp : document.getElementById("store-wp"),
			android : document.getElementById("store-android"),
			other : document.getElementById("store-other"),
		};
		for (var platform in platforms) {
			platforms[platform].style.display = platform == mobilePlatform ? "inherit" : "none";
		}
		switchToScreen("mobile");
	};

	// Game hits a runtime error that prevents to continue. 
	var OnFatalError = function(error) {
		switchToScreen("fatal");
	};

	// Could be used as a fallback to flash.
	var OnMemoryError = function(host64bit) {
		if (host64bit) {
			// If host is 64Bit we can suggest to install 64bit version of browser whats fixes problem with memory
			switchToScreen("memory-error");
		} else {
			// Otherwise (unlikely case) we can act like aby other error.
			switchToScreen("fatal");
		}
	};

	var OnLoadingStatus = function(status, progress) {
		statusElement.innerHTML = status;
		progressElement.children[0].style.width = progress  + "%";
		switchToScreen("loading");
	};

	// Game code has constructed.
	var OnLoadingDone = function() {
		switchToScreen("game");
	};

	// Could be used as a fallback to flash.
	var OnMissingWebGL = function() {
		switchToScreen("no-webgl");
	};

	return {
		Init : Init,
		SetViewSize : SetViewSize,
		OnBrowserIncompatible : OnBrowserIncompatible,
		OnMissingWebGL : OnMissingWebGL,
		OnMobileBrowser : OnMobileBrowser,
		OnFatalError : OnFatalError,
		OnMemoryError : OnMemoryError,
		OnLoadingStatus : OnLoadingStatus,
		OnLoadingDone : OnLoadingDone,
	};
}();

