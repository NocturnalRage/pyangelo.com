// PyAngelo Canvas
Sk.PyAngelo = {};
Sk.PyAngelo.canvas = document.getElementById("canvas");
Sk.PyAngelo.ctx = Sk.PyAngelo.canvas.getContext("2d");
Sk.PyAngelo.images = {};
Sk.PyAngelo.keys = {};
Sk.PyAngelo.keyWasPressed = {};

Sk.PyAngelo.canvas.addEventListener("keydown", _keydown);
Sk.PyAngelo.canvas.addEventListener("keyup", _keyup);
Sk.PyAngelo.canvas.addEventListener("mousemove", _canvasMouseMove);
Sk.PyAngelo.canvas.addEventListener("mousedown", _canvasMouseDown);
Sk.PyAngelo.canvas.addEventListener("mouseup", _canvasMouseUp);

// Ensure input element gets focus
// if there is one when console is clicked
Sk.PyAngelo.console = document.getElementById("console");
Sk.PyAngelo.console.addEventListener("mousedown", _consoleMouseDown);


// Setup global Python Variables
// Maths
Sk.builtins.QUARTER_PI = new Sk.builtin.float_(0.7853982);
Sk.builtins.HALF_PI = new Sk.builtin.float_(1.57079632679489661923);
Sk.builtins.PI = new Sk.builtin.float_(3.14159265358979323846);
Sk.builtins.TWO_PI = new Sk.builtin.float_(6.28318530717958647693);
Sk.builtins.TAU = new Sk.builtin.float_(6.28318530717958647693);
// Used to set y axis mode
Sk.builtins.CARTESIAN = new Sk.builtin.int_(1);
Sk.builtins.JAVASCRIPT = new Sk.builtin.int_(2);
// Used to set angle mode
Sk.builtins.RADIANS = new Sk.builtin.int_(1);
Sk.builtins.DEGREES = new Sk.builtin.int_(2);
// Used for rect mode and circle mode
Sk.builtins.CORNER = new Sk.builtin.int_(1);
Sk.builtins.CORNERS = new Sk.builtin.int_(2);
Sk.builtins.CENTER = new Sk.builtin.int_(3);
// Used for end shape
Sk.builtins.CLOSE = new Sk.builtin.int_(1);
Sk.builtins.OPEN = new Sk.builtin.int_(2);
// Used for console height
Sk.builtins.SMALL = new Sk.builtin.int_(300);
Sk.builtins.MEDIUM = new Sk.builtin.int_(500);
Sk.builtins.LARGE = new Sk.builtin.int_(1000);

// Global Python Colours
Sk.PyAngelo.textColour = "rgba(147, 161, 161, 1)";
Sk.PyAngelo.highlightColour = "rgba(0, 0, 0, 1)";
Sk.builtins.YELLOW = 0;
Sk.builtins.ORANGE = 1;
Sk.builtins.RED = 2;
Sk.builtins.MAGENTA = 3;
Sk.builtins.VIOLET = 4;
Sk.builtins.BLUE = 5;
Sk.builtins.CYAN = 6;
Sk.builtins.GREEN = 7;
Sk.builtins.WHITE = 8;
Sk.builtins.GRAY = 9;
Sk.builtins.GREY = 9;
Sk.builtins.DEFAULT = 9;
Sk.builtins.BLACK = 10;

// GLOBAL KEYS
Sk.builtins.KEY_A = "KeyA";
Sk.builtins.KEY_B = "KeyB";
Sk.builtins.KEY_C = "KeyC";
Sk.builtins.KEY_D = "KeyD";
Sk.builtins.KEY_E = "KeyE";
Sk.builtins.KEY_F = "KeyF";
Sk.builtins.KEY_G = "KeyG";
Sk.builtins.KEY_H = "KeyH";
Sk.builtins.KEY_I = "KeyI";
Sk.builtins.KEY_J = "KeyJ";
Sk.builtins.KEY_K = "KeyK";
Sk.builtins.KEY_L = "KeyL";
Sk.builtins.KEY_M = "KeyM";
Sk.builtins.KEY_N = "KeyN";
Sk.builtins.KEY_O = "KeyO";
Sk.builtins.KEY_P = "KeyP";
Sk.builtins.KEY_Q = "KeyQ";
Sk.builtins.KEY_R = "KeyR";
Sk.builtins.KEY_S = "KeyS";
Sk.builtins.KEY_T = "KeyT";
Sk.builtins.KEY_U = "KeyU";
Sk.builtins.KEY_V = "KeyV";
Sk.builtins.KEY_W = "KeyW";
Sk.builtins.KEY_X = "KeyX";
Sk.builtins.KEY_Y = "KeyY";
Sk.builtins.KEY_Z = "KeyZ";
Sk.builtins.KEY_SPACE = "Space";
Sk.builtins.KEY_ENTER = "Enter";
Sk.builtins.KEY_ESC = "Escape";
Sk.builtins.KEY_DEL = "Delete";
Sk.builtins.KEY_BACKSPACE = "Backspace";
Sk.builtins.KEY_TAB = "Tab";
Sk.builtins.KEY_LEFT = "ArrowLeft";
Sk.builtins.KEY_RIGHT = "ArrowRight";
Sk.builtins.KEY_UP = "ArrowUp";
Sk.builtins.KEY_DOWN = "ArrowDown";

// Add mouse handlers
function _setMousePosition(ev) {
    boundingRect = Sk.PyAngelo.canvas.getBoundingClientRect();
    Sk.builtins.mouseX = Sk.ffi.remapToPy(ev.clientX - boundingRect.left);
    Sk.builtins.mouseY = Sk.ffi.remapToPy(ev.clientY - boundingRect.top);
}
function _canvasMouseMove(ev) {
    ev.preventDefault();
    _setMousePosition(ev);
}

function _canvasMouseDown(ev) {
    ev.preventDefault();
    _setMousePosition(ev);
    Sk.builtins.mouseIsPressed = Sk.builtin.bool.true$;
    Sk.PyAngelo.canvas.focus();
}

function _canvasMouseUp(ev) {
    ev.preventDefault();
    _setMousePosition(ev);
    Sk.builtins.mouseIsPressed = Sk.builtin.bool.false$;
}

function _consoleMouseDown(ev) {
    let inputElement
    if (inputElement = document.getElementById("inputElement")) {
        inputElement.focus();
        ev.preventDefault();
    }
}

function _keydown(ev) {
    ev.preventDefault();
    Sk.PyAngelo.keys[ev.code] = true;
    Sk.PyAngelo.keyWasPressed[ev.code] = true;
}

function _keyup(ev) {
    ev.preventDefault();
    Sk.PyAngelo.keys[ev.code] = false;
    Sk.PyAngelo.keyWasPressed[ev.code] = false;
}

// Used for sound
Sk.PyAngelo.sounds = {};

// Allow skulpt script to be stopped
let _stopExecution = false;

function createColouredTextSpanElement(text) {
  let spanElement = document.createElement("span");
  spanElement.appendChild(document.createTextNode(text));
  spanElement.style.color = Sk.PyAngelo.textColour;
  spanElement.style.backgroundColor = Sk.PyAngelo.highlightColour;
  return spanElement;
}

function outf(text) {
  Sk.PyAngelo.console.appendChild(createColouredTextSpanElement(text));
  Sk.PyAngelo.console.scrollTop = document.getElementById("console").scrollHeight
}

function stopSkulpt() {
  stopCode();
}

function runSkulpt(code) {
    Sk.PyAngelo.ctx.save();
    Sk.PyAngelo.textColour = "rgba(147, 161, 161, 1)";
    Sk.PyAngelo.highlightColour = "rgba(0, 0, 0, 1)";
    Sk.PyAngelo.console.innerHTML = '';
    Sk.PyAngelo.keys = {};
    Sk.PyAngelo.keyWasPressed = {};
    Sk.builtins._angleModeValue = new Sk.builtin.int_(2);
    Sk.builtins._doFill = Sk.builtin.bool.true$;
    Sk.builtins._doStroke = Sk.builtin.bool.true$;
    Sk.builtins._rectMode = new Sk.builtin.int_(1);
    Sk.builtins._circleMode = new Sk.builtin.int_(3);
    Sk.builtins.width = new Sk.builtin.int_(0);
    Sk.builtins.height = new Sk.builtin.int_(0);
    Sk.builtins.mouseX = new Sk.builtin.int_(0);
    Sk.builtins.mouseY = new Sk.builtin.int_(0);
    Sk.builtins.mouseIsPressed = Sk.builtin.bool.false$;

    Sk.inputfun = function(prompt) {
        return new Promise(function (resolve, reject) {
            let inputElement = document.createElement("span");
            inputElement.setAttribute('contenteditable', 'true');
            inputElement.style.backgroundColor = Sk.PyAngelo.highlightColour;
            inputElement.style.color = Sk.PyAngelo.textColour;
            inputElement.style.outlineStyle = "none";
            inputElement.id = "inputElement";
            Sk.PyAngelo.console.appendChild(inputElement);
            inputElement.focus()
            inputElement.addEventListener("keyup", function(e) {
                e.preventDefault();
                if (e.key === 'Enter') {
                    userResponse = inputElement.innerText.replace(/\n+$/, ''); 
                    inputElement.remove();
                    outf(userResponse);
                    outf("\n");
                    resolve(userResponse); 
                }
            });
        });
    };

    Sk.configure({
        output:outf,
        inputfunTakesPrompt: false,
        debugging: false,
        goto: true,
        killableWhile: true,
        killableFor: false,
        __future__: Sk.python3
    });

    if (document.getElementById("debug").checked) {
      Sk.debugging = true;
      console.log("Running in debug mode");
    } else {
      Sk.debugging = false;
    }

    Sk.onBeforeImport = function() {
        return Sk.misceval.promiseToSuspension(new Promise(function(resolve, reject) {
            setTimeout(function() {
                resolve();
            }, 10);
        }));
    };

    function sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
    }

    let currentLineNo = 0
    async function lineStepper(susp) {
        try {
          if (currentLineNo !== susp.child.$lineno) {
            currentLineNo = susp.child.$lineno;
            checkForStop();
            editor.gotoLine(currentLineNo);
            await sleep(1000);
          }
          return Promise.resolve(susp.resume());
        } catch(e) {
          return Promise.reject(e);
        }
    }

    function checkForStop() {
      if (_stopExecution) {
        Sk.builtin.stopAllSounds();
        throw "Program stopped!";
      }
    }

    let myPromise;
    if (Sk.debugging) {
      myPromise = Sk.misceval.asyncToPromise(function() {
        return Sk.importMainWithBody("<stdin>", true, code, true);
      },{
        // handle a suspension of the executing code
        // "*" says handle all types of suspensions
        "*": lineStepper
      });
    } else {
      myPromise = Sk.misceval.asyncToPromise(function() {
        return Sk.importMainWithBody("<stdin>", true, code, true);
      },{
        // handle a suspension of the executing code
        // "*" says handle all types of suspensions
        "*": checkForStop
      });
    }

    myPromise.then(function(mod) {}, function(err) {
        let tc = Sk.PyAngelo.textColour;
        let hc = Sk.PyAngelo.highlightColour;
        Sk.PyAngelo.textColour = "rgba(255, 0, 0, 1)";
        Sk.PyAngelo.highlightColour = "rgba(255, 255, 255, 1)";
        if (err.message) {
            outf(err.message + "\n");
            outf(err.stack);
        } else if (err.nativeError) {
            outf(err.nativeError.message + "\n");
            outf(err.nativeError.stack);
        } else {
            outf(err.toString());
            outf(err.stack || "")
        }
        Sk.PyAngelo.textColour = tc;
        Sk.PyAngelo.highlightColour = hc;
    });
    myPromise.finally(function() {stopSkulpt()});
    Sk.PyAngelo.ctx.restore();
}
