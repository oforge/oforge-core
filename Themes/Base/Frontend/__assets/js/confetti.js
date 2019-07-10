"use strict";

function _instanceof(left, right) { if (right != null && typeof Symbol !== "undefined" && right[Symbol.hasInstance]) { return right[Symbol.hasInstance](left); } else { return left instanceof right; } }

function _classCallCheck(instance, Constructor) { if (!_instanceof(instance, Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

/**
 * Adds confetti animation
 */
(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'confetti',
            selector: '[data-confetti]',
            init: function init() {
                console.log("much confetti");
                /**
                 * Confetti particle class
                 */

                var ConfettiParticle =
                    /*#__PURE__*/
                    function () {
                        function ConfettiParticle(context, width, height) {
                            _classCallCheck(this, ConfettiParticle);

                            this.context = context;
                            this.width = width;
                            this.height = height;
                            this.color = '';
                            this.lightness = 50;
                            this.diameter = 0;
                            this.tilt = 0;
                            this.tiltAngleIncrement = 0;
                            this.tiltAngle = 0;
                            this.particleSpeed = 1;
                            this.waveAngle = 0;
                            this.x = 0;
                            this.y = 0;
                            this.reset();
                        }

                        _createClass(ConfettiParticle, [{
                            key: "reset",
                            value: function reset() {
                                this.lightness = 50;
                                this.color = Math.floor(Math.random() * 360);
                                this.x = Math.random() * this.width;
                                this.y = Math.random() * this.height - this.height;
                                this.diameter = Math.random() * 6 + 4;
                                this.tilt = 0;
                                this.tiltAngleIncrement = Math.random() * 0.1 + 0.04;
                                this.tiltAngle = 0;
                            }
                        }, {
                            key: "darken",
                            value: function darken() {
                                if (this.y < 200 || this.lightness <= 0) return;
                                this.lightness -= 250 / this.height;
                            }
                        }, {
                            key: "update",
                            value: function update() {
                                this.waveAngle += this.tiltAngleIncrement;
                                this.tiltAngle += this.tiltAngleIncrement;
                                this.tilt = Math.sin(this.tiltAngle) * 12;
                                this.x += Math.sin(this.waveAngle);
                                this.y += (Math.cos(this.waveAngle) + this.diameter + this.particleSpeed) * 0.4;
                                if (this.complete()) this.reset();
                                this.darken();
                            }
                        }, {
                            key: "complete",
                            value: function complete() {
                                return this.y > this.height + 20;
                            }
                        }, {
                            key: "draw",
                            value: function draw() {
                                var x = this.x + this.tilt;
                                this.context.beginPath();
                                this.context.lineWidth = this.diameter;
                                this.context.strokeStyle = "hsl(" + this.color + ", 50%, " + this.lightness + "%)";
                                this.context.moveTo(x + this.diameter / 2, this.y);
                                this.context.lineTo(x, this.y + this.tilt + this.diameter / 2);
                                this.context.stroke();
                            }
                        }]);

                        return ConfettiParticle;
                    }();
                /**
                 * Setup
                 */


                (function () {
                    var width = window.innerWidth;
                    var height = window.innerHeight;
                    var particles = []; // particle canvas

                    var canvas = document.createElement('canvas');
                    var context = canvas.getContext('2d');
                    canvas.id = 'particle-canvas';
                    canvas.width = width;
                    canvas.height = height;
                    document.body.appendChild(canvas); // update canvas size

                    var updateSize = function updateSize() {
                        width = window.innerWidth;
                        height = window.innerHeight;
                        canvas.width = width;
                        canvas.height = height;
                    }; // create confetti particles


                    var createParticles = function createParticles() {
                        particles = [];
                        var total = 50;

                        if (width > 1280) {
                            total = 200;
                        } else if (width > 960) {
                            total = 150;
                        } else if (width > 640) {
                            total = 100;
                        }

                        for (var i = 0; i < total; ++i) {
                            particles.push(new ConfettiParticle(context, width, height));
                        }
                    }; // animation loop function


                    var animationFunc = function animationFunc() {
                        requestAnimationFrame(animationFunc);
                        context.clearRect(0, 0, width, height);

                        for (var _i = 0, _particles = particles; _i < _particles.length; _i++) {
                            var p = _particles[_i];
                            p.width = width;
                            p.height = height;
                            p.update();
                            p.draw();
                        }
                    }; // on resize


                    window.addEventListener('resize', function (e) {
                        updateSize();
                        createParticles();
                    }); // start

                    updateSize();
                    createParticles();
                    animationFunc();
                })();
            }
        });
    } else {
        console.warn("Oforge is not defined. Module cannot be registered.");
    }
})();
