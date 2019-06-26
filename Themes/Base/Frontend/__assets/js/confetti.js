/**
 * Adds confetti animation
 */

(function () {
    if (typeof Oforge !== 'undefined') {
        Oforge.register({
            name: 'confetti',
            selector: '[data-confetti]',
            init: function () {
                console.log("much confetti");
                /**
                 * Confetti particle class
                 */
                class ConfettiParticle {

                    constructor( context, width, height ) {
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

                    reset() {
                        this.lightness = 50;
                        this.color = Math.floor( Math.random() * 360 );
                        this.x = Math.random() * this.width;
                        this.y = Math.random() * this.height - this.height;
                        this.diameter = Math.random() * 6 + 4;
                        this.tilt = 0;
                        this.tiltAngleIncrement = Math.random() * 0.1 + 0.04;
                        this.tiltAngle = 0;
                    }

                    darken() {
                        if ( this.y < 200 || this.lightness <= 0 ) return;
                        this.lightness -= ( 250 / this.height );
                    }

                    update() {
                        this.waveAngle += this.tiltAngleIncrement;
                        this.tiltAngle += this.tiltAngleIncrement;
                        this.tilt = Math.sin( this.tiltAngle ) * 12;
                        this.x += Math.sin( this.waveAngle );
                        this.y += ( Math.cos( this.waveAngle ) + this.diameter + this.particleSpeed ) * 0.4;
                        if ( this.complete() ) this.reset();
                        this.darken();
                    }

                    complete() {
                        return ( this.y > this.height + 20 );
                    }

                    draw() {
                        let x = this.x + this.tilt;
                        this.context.beginPath();
                        this.context.lineWidth = this.diameter;
                        this.context.strokeStyle = "hsl("+ this.color +", 50%, "+this.lightness+"%)";
                        this.context.moveTo( x + this.diameter / 2, this.y );
                        this.context.lineTo( x, this.y + this.tilt + this.diameter / 2 );
                        this.context.stroke();
                    }
                }

                /**
                 * Setup
                 */
                (function() {
                    let width = window.innerWidth;
                    let height = window.innerHeight;
                    let particles = [];

                    // particle canvas
                    const canvas = document.createElement( 'canvas' );
                    const context = canvas.getContext( '2d' );
                    canvas.id = 'particle-canvas';
                    canvas.width = width;
                    canvas.height = height;
                    document.body.appendChild( canvas );

                    // change body bg color


                    // update canvas size
                    const updateSize = () => {
                        width = window.innerWidth;
                        height = window.innerHeight;
                        canvas.width = width;
                        canvas.height = height;
                    };

                    // create confetti particles
                    const createParticles = () => {
                        particles = [];
                        let total = 100;

                        if ( width > 1080 ) { total = 400; } else
                        if ( width > 760 )  { total = 300; } else
                        if ( width > 520 )  { total = 200; }

                        for ( let i = 0; i < total; ++i ) {
                            particles.push( new ConfettiParticle( context, width, height ) );
                        }
                    };

                    // animation loop function
                    const animationFunc = () => {
                        requestAnimationFrame( animationFunc );
                        if ( Math.random() > 0.98 ) changeBgColor();
                        context.clearRect( 0, 0, width, height );

                        for ( let p of particles ) {
                            p.width = width;
                            p.height = height;
                            p.update();
                            p.draw();
                        }
                    };

                    // on resize
                    window.addEventListener( 'resize', e => {
                        updateSize();
                        createParticles();
                    });

                    // start
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
