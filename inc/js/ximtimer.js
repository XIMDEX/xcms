/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

var XimTimer = function(interval) {

	this.timeHandler = null;
	this.observers = null;
	this.interval = null;

	this._init = function(interval) {
		this.observers = [];
		this.interval = interval || 20;
	};

	this.getObserver = function(observer) {
		var index = isNaN(observer) ? null : observer;
		var i = 0;
		while (i<this.observers.length && index === null) {
			var obs = this.observers[i];
			if (obs.observer == observer) {
				index = i;
			}
			i++;
		}
		// return index or null
		return index;
	};

	this.addObserver = function(observer, interval) {
		if (!interval) return null;
		var index = this.getObserver(observer);
		if (index) return index;
		this.observers.push({
			interval: interval,
			lastExecuted: null,
			observer: observer
		});
		return (this.observers.length - 1);
	};

	this.removeObserver = function(observer) {
		var index = this.getObserver(observer);
		if (index !== null) this.observers.splice(index, 1);
	};
	
	this.removeAllObservers = function() {
		for (var i=0,l=this.observers.length; i<l; i++) {
			this.removeObserver(this.observers[i]);
		}
		this.observers = [];
	};

	this.start = function() {
		if (this.timeHandler !== null) return;
		this._timeEvent();
	};

	this.stop = function() {
		if (this.timeHandler === null) return;
		clearTimeout(this.timeHandler);
		this.timeHandler = null;
	};

	this._timeEvent = function() {
		var now = new Date().getTime();
		for (var i=0; i<this.observers.length; i++) {
			var obs = this.observers[i];
			if (obs.lastExecuted === null) {
				obs.lastExecuted = new Date().getTime();
			}
			if ((now - obs.lastExecuted) >= obs.interval) {
				obs.lastExecuted = new Date().getTime();
				obs.observer();
			}
		}
		this.timeHandler = setTimeout(function() {this._timeEvent();}.bind(this), this.interval);
	};

	this._init(interval);
};


XimTimer.instance = null;

XimTimer.getInstance = function() {
	if (XimTimer.instance === null) {
		XimTimer.instance = new XimTimer();
	}
	return XimTimer.instance;
};
