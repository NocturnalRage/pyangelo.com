import sys
import time
import traceback
import javascript
import random
from browser import document, window, alert, timer, bind, self, html, load

load("/js/howler.js")

from pyangelo_consts import *

class PyAngeloImage():
    def __init__(self, image, sprite):
        self.img = image
        self.height = image.naturalHeight
        self.width = image.naturalWidth
        self.sprite = sprite

class Point():
    def __init__(self, x = 0, y = 0):
        self.x = x
        self.y = y

class Colour():
    def __init__(self, r = 255, g = 255, b = 255, a = 1.0):
        self.r = r
        self.g = g
        self.b = b
        self.a = a

class Sprite:
    def __init__(self, image, x = 0, y = 0, width = 0, height = 0, opacity = 1.0):
        self.x = x
        self.y = y
        self.width = width
        self.height = height
        self.opacity = opacity
        self.image = loadImage(image, self)

    def draw(self, offsetX = 0, offsetY = 0):
        image(self.image, self.x - offsetX, self.y - offsetY, self.width, self.height, opacity=self.opacity)

    def moveBy(self, x, y):
        self.x += x
        self.y += y

    def moveTo(self, x, y):
        self.x = x
        self.y = y

    def leftBoundary(self):
        return self.x

    def rightBoundary(self):
        return self.x + self.width

    def topBoundary(self):
        return self.y

    def bottomBoundary(self):
        return self.y + self.height

    def overlaps(self, other):
        return self.leftBoundary() < other.rightBoundary() and self.rightBoundary() > other.leftBoundary() and self.topBoundary() < other.bottomBoundary() and self.bottomBoundary() > other.topBoundary()

    def contains(self, point):
        return point.x >= self.leftBoundary() and point.x <= self.rightBoundary() and point.y >= self.topBoundary() and point.y <= self.bottomBoundary()

class TextSprite(Sprite):
    def __init__(self, text, x = 0, y = 0, fontSize = 20, fontName = "Arial", r = 255, g = 255, b = 255, a = 1.0):
        self.text = text
        self.x = x
        self.y = y
        self.fontSize = fontSize
        self.fontName = fontName
        self.setColour(r, g, b)
        # Get Text Width
        _ctx.font = str(fontSize) + "pt " + self.fontName
        textMetrics = _ctx.measureText(self.text)
        self.width = abs(textMetrics.actualBoundingBoxLeft) + abs(textMetrics.actualBoundingBoxRight)
        self.height = abs(textMetrics.actualBoundingBoxAscent) + abs(textMetrics.actualBoundingBoxDescent)

    def center(self):
        self.x -= (self.width/2)
        self.y -= (self.height/2)

    def draw(self):
        fs = _ctx.fillStyle
        _ctx.fillStyle = "rgba(" + str(self.r) + "," + str(self.g) + "," + str(self.b) + "," + str(self.a)+ ")"
        text(self.text, self.x, self.y, self.fontSize, self.fontName)
        _ctx.fillStyle = fs

    def setColour(self, r, g, b, a = 1.0):
        self.r = r
        self.g = g
        self.b = b
        self.a = a

class RectangleSprite(TextSprite):
    def __init__(self, x, y, width, height, r = 255, b = 255, g = 255, a = 1.0):
        self.x = x
        self.y = y
        self.width = width
        self.height = height
        self.setColour(r, g, b)
        self.strokeWeight(1)
        self.stroke(0, 0, 0, 1)
        self.noStroke()

    def noStroke(self):
        self._doStroke = False

    def stroke(self, r, g, b, a = 1):
        self.stroke_r = r
        self.stroke_g = g
        self.stroke_b = b
        self.stroke_a = a
        self._doStroke = True

    def strokeWeight(self, weight):
        self._strokeWeight = weight

    def draw(self):
        global _doStroke
        fs = _ctx.fillStyle
        _ctx.fillStyle = "rgba(" + str(self.r) + "," + str(self.g) + "," + str(self.b) + "," + str(self.a)+ ")"
        ss = _ctx.strokeStyle
        lw = _ctx.lineWidth
        _ctx.lineWidth = self._strokeWeight
        _ctx.strokeStyle = "rgba(" + str(self.stroke_r) + "," + str(self.stroke_g) + "," + str(self.stroke_b) + "," + str(self.stroke_a)+ ")"
        ds = _doStroke
        _doStroke = self._doStroke

        self.drawShape()

        _ctx.fillStyle = fs
        _ctx.strokeStyle = ss
        _ctx.lineWidth = lw
        _doStroke = ds

    def drawShape(self):
        rect(self.x, self.y, self.width, self.height)

class CircleSprite(RectangleSprite):
    def __init__(self, x, y, radius, r = 255, b = 255, g = 255, a = 1.0):
        # Set to enable standard collision detection in overlaps method
        self.x = x
        self.y = y
        self.radius = radius
        self.diameter = radius * 2
        self.setColour(r, g, b, a)
        self.strokeWeight(1)
        self.stroke(0, 0, 0, 1)
        self.noStroke()

    def leftBoundary(self):
        return self.x - self.radius

    def rightBoundary(self):
        return self.x + self.radius

    def topBoundary(self):
        return self.y - self.radius

    def bottomBoundary(self):
        return self.y + self.radius

    def drawShape(self):
        circle(self.x, self.y, self.radius)

class EllipseSprite(RectangleSprite):
    def __init__(self, x, y, radiusX, radiusY, r = 255, b = 255, g = 255, a = 1.0):
        # Set to enable standard collision detection in overlaps method
        self.x = x
        self.y = y
        self.radiusX = radiusX
        self.radiusY = radiusY
        self.setColour(r, g, b, a)
        self.strokeWeight(1)
        self.stroke(0, 0, 0, 1)
        self.noStroke()

    def leftBoundary(self):
        return self.x - self.radiusX

    def rightBoundary(self):
        return self.x + self.radiusX

    def topBoundary(self):
        return self.y - self.radiusY

    def bottomBoundary(self):
        return self.y + self.radiusY

    def drawShape(self):
        ellipse(self.x, self.y, self.radiusX, self.radiusY)

def _setMousePosition(ev):
    global mouseX, mouseY
    boundingRect = _canvas.getBoundingClientRect()
    mouseX = int(ev.clientX - boundingRect.left)
    mouseY = int(ev.clientY - boundingRect.top)

def _mousemove(ev):
    ev.preventDefault()
    _setMousePosition(ev)

def _mousedown(ev):
    global mouseIsPressed
    ev.preventDefault()
    _setMousePosition(ev)
    mouseIsPressed = True

def _mouseup(ev):
    global mouseIsPressed
    ev.preventDefault()
    _setMousePosition(ev)
    mouseIsPressed = False

def loadSound(filename, loop = False, streaming = False):
    global _loadingResources
    howl = window.Howl
    sound = howl.new({"src": [filename], "loop": loop, "onload": _soundLoaded})
    _loadingResources += 1

    _soundPlayers[filename] = sound
    return filename

def _soundLoaded(e, f):
    global _loadingResources
    _loadingResources -= 1

def playSound(sound, loop = False, volume = 1.0):
    if sound not in _soundPlayers:
        sound = loadSound(sound)

    _soundPlayers[sound].loop(loop)
    _soundPlayers[sound].volume(volume)
    _soundPlayers[sound].play()

def stopAllSounds():
    for sound in _soundPlayers:
        stopSound(sound)

def pauseSound(sound):
    if sound in _soundPlayers:
        _soundPlayers[sound].pause()

# alias for pauseSound
def stopSound(sound):
    if sound in _soundPlayers:
        soundPlayers[sound].stop()

def getPixelColour(x, y):
    pixel = window.Int8Array.new(4)
    imageData = _ctx.getImageData(x, y, 1, 1)
    return Colour(imageData.data[0], imageData.data[1], imageData.data[2], imageData.data[3])

def _start():
    global _state
    if _state != STATE_RUN:
        _state = STATE_RUN

def _stop():
    global _state, _loadingResources
    if _state != STATE_STOP:
        _state = STATE_STOP
        _loadingResources = 0
        stopAllSounds()

def _update(deltaTime):
    global _state
    if _state == STATE_STOP:
        background(100, 149, 237)
        ready = TextSprite("Ready", width/2, height/2, 60)
        ready.center()
        ready.draw()
    elif _state == STATE_RUN:
        if _main_loop_func is not None:
            try:
                _main_loop_func()
            except Exception as e:
                do_print("Error: " + str(e) + "\n" + traceback.format_exc(), "red")
                _stop()

    timer.request_animation_frame(_update)

def _loop(func):
    global _main_loop_func
    _main_loop_func = func

def _resourceError(e):
    _stop()
    do_print("Error loading of resource: " + e.target.src + "\n", "red")

def _resourceAbort(e):
    _stop()
    do_print("Aborted loading of resource: " + e.target.src + "\n", "red")

def _resourceLoaded(e):
    global _loadingResources
    window.console.log("Successfully loaded file:" + e.target.src)

    e.target.pyangeloImage.height = e.target.naturalHeight
    e.target.pyangeloImage.width = e.target.naturalWidth

    if e.target.pyangeloImage.sprite is not None:
        if e.target.pyangeloImage.sprite.height == 0:
            e.target.pyangeloImage.sprite.height = e.target.naturalHeight
        if e.target.pyangeloImage.sprite.width == 0:
            e.target.pyangeloImage.sprite.width = e.target.naturalWidth

    _loadingResources -= 1

def loadImage(file, sprite = None):
    global _loadingResources
    _loadingResources += 1

    window.console.log("Attempting to load file:" + file)
    img = html.IMG()
    img.crossOrigin = "Anonymous"
    img.src = file

    img.bind('load', _resourceLoaded)
    img.bind('error', _resourceError)
    img.bind('abort', _resourceAbort)

    pyangeloImage = PyAngeloImage(img, sprite)
    img.pyangeloImage = pyangeloImage

    return pyangeloImage

def image(image, x, y, width = None, height = None, opacity=None):
    if width is None:
        width = image.width

    if height is None:
        height = image.height

    ga = _ctx.globalAlpha
    if opacity is not None:
        if opacity > 1.0:
            opacity = 1.0
        elif opacity < 0.0:
            opacity = 0.0
        _ctx.globalAlpha = opacity

    _ctx.drawImage(image.img, x, y, width, height)

    _ctx.globalAlpha = ga

def text(text, x, y, fontSize = 10, fontName = "Arial"):
    _ctx.font = str(fontSize) + "pt " + fontName
    _ctx.textBaseline = "top"
    _ctx.fillText(text, x, y)

def setCanvasSize(w, h):
    global width, height
    fs = _ctx.fillStyle
    ss = _ctx.strokeStyle
    _canvas["width"] = w
    _canvas["height"] = h
    width = _canvas.width
    height = _canvas.height
    _ctx.fillStyle = fs
    _ctx.strokeStyle = ss

def background(r = 0, g = 0, b = 0, a = 1):
    fs = _ctx.fillStyle
    _ctx.fillStyle = "rgba(" + str(r) + "," + str(g) + "," + str(b) + "," + str(a)+ ")"
    _ctx.fillRect(0, 0, width, height)
    _ctx.fillStyle = fs

def saveState():
    _ctx.save()

def restoreState():
    _ctx.restore()

def translate(x, y):
    _ctx.translate(x, y)

def rotate(angle):
    if _angleMode != RADIANS:
        angle = PI/180 * angle
    _ctx.rotate(angle)

def strokeWeight(weight):
    _ctx.lineWidth = weight

def fill(r=255, g=255, b=255, a=1.0):
    global _doFill
    _ctx.fillStyle = "rgba(" + str(r) + "," + str(g) + "," + str(b) + "," + str(a) + ")"
    _doFill = True

def noFill():
    global _doFill
    _doFill = False

def _fill():
    if _doFill:
        _ctx.fill()

def stroke(r=0, g=0, b=0, a=1.0):
    global _doStroke;
    _ctx.strokeStyle = "rgba(" + str(r) + "," + str(g) + "," + str(b) + "," + str(a) + ")"
    _doStroke = True

def noStroke():
    global _doStroke;
    _doStroke = False

def _stroke():
    if _doStroke:
        _ctx.stroke()

def angleMode(mode):
    global _angleMode
    if mode == RADIANS or mode == DEGREES:
        _angleMode = mode

def rectMode(mode):
    global _rectMode
    if mode == CORNER or mode == CORNERS or mode == CENTER:
        _rectMode = mode

def circleMode(mode):
    global _circleMode
    if mode == CORNER or mode == CENTER:
        _circleMode = mode

def line(x1, y1, x2, y2):
    _ctx.beginPath()
    _ctx.moveTo(x1, y1)
    _ctx.lineTo(x2, y2)
    _stroke()

def circle(x, y, radius):
    if _circleMode == CORNER:
        x = x + radius
        y = y + radius
    _ctx.beginPath()
    _ctx.arc(x, y, radius, 0, TWO_PI)
    _fill()
    _stroke()

def ellipse(x, y, radiusX, radiusY):
    if _circleMode == CORNER:
        x = x + radiusX
        y = y + radiusY
    _ctx.beginPath()
    _ctx.ellipse(x, y, radiusX, radiusY, 0, 0, TWO_PI)
    _fill()
    _stroke()

def arc(x, y, radiusX, radiusY, startAngle, endAngle):
    if _circleMode == CORNER:
        x = x + radiusX
        y = y + radiusY
    if _angleMode != RADIANS:
        startAngle = PI/180 * startAngle
        endAngle = PI/180 * endAngle
    _ctx.beginPath()
    _ctx.ellipse(x, y, radiusX, radiusY, 0, startAngle, endAngle)
    _fill()
    _stroke()

def triangle(x1, y1, x2, y2, x3, y3):
    _ctx.beginPath()
    _ctx.moveTo(x1, y1)
    _ctx.lineTo(x2, y2)
    _ctx.lineTo(x3, y3)
    _ctx.closePath()
    _fill()
    _stroke()

def quad(x1, y1, x2, y2, x3, y3, x4, y4):
    _ctx.beginPath()
    _ctx.moveTo(x1, y1)
    _ctx.lineTo(x2, y2)
    _ctx.lineTo(x3, y3)
    _ctx.lineTo(x4, y4)
    _ctx.closePath()
    _fill()
    _stroke()

def point(x, y):
    if _doStroke:
        s = _ctx.strokeStyle
        f = _ctx.fillStyle
        _ctx.fillStyle = s

        _ctx.beginPath()
        if _ctx.lineWidth > 1:
            _ctx.arc(x, y, _ctx.lineWidth / 2, 0, TWO_PI)
        else:
            rect(x, y, 1, 1)
        _fill()

        _ctx.fillStyle = f

def square(x, y, l):
    _ctx.beginPath()
    _ctx.rect(x, y, l, l)
    _fill()
    _stroke()

def rect(x, y, w, h):
    if _rectMode == CORNERS:
        w = w - x
        h = h - y
    elif _rectMode == CENTER:
        x = x - w * 0.5
        y = y - h * 0.5
    _ctx.beginPath()
    _ctx.rect(x, y, w, h)
    _fill()
    _stroke()

def _keydown(ev):
    _keys[ev.which] = True

def _keyup(ev):
    _keys[ev.which] = False

def isKeyPressed(key):
    return _keys[key]

_state = STATE_STOP
_main_loop_func = None
_soundPlayers = {}
_loadingResources = 0
_canvas = document["canvas"]
_ctx = _canvas.getContext('2d')		
width = 0
height = 0
setCanvasSize(DEFAULT_WIDTH, DEFAULT_HEIGHT)

_keys = dict([(a, False) for a in range(255)] +
                 [(a, False) for a in range(0xff00, 0xffff)])
_keys[KEY_V_LEFT] = False
_keys[KEY_V_RIGHT] = False
_keys[KEY_V_UP] = False
_keys[KEY_V_DOWN] = False
_keys[KEY_V_FIRE] = False

document.bind("keydown", _keydown)
document.bind("keyup", _keyup)

mouseIsPressed = False
mouseX = 0
mouseY = 0
_canvas.bind("mousedown", _mousedown)
_canvas.bind("mouseup", _mouseup)
_canvas.bind("mousemove", _mousemove)

_doFill = True
_doStroke = True
_angleMode = DEGREES
_rectMode = CORNER
_circleMode = CENTER
_state = STATE_STOP
timer.request_animation_frame(_update)

window.canvas_stop = _stop

def format_string_HTML(s):
    return s.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;").replace("\n", "<br>").replace("\"", "&quot;").replace("'", "&apos;").replace(" ", "&nbsp;")

def do_print(s, color=None):
    if color is not None:
        window.writeOutput("<p style='display:inline;color:" + color + ";'>" + format_string_HTML(s) + "</p>", True)
    else:
        window.writeOutput("<p style='display:inline;'>" + format_string_HTML(s) + "</p>", True)


pre_globals = []
def startSketch(src):
    global _doFill, _doStroke, _angleMode, _rectMode, _circleMode, _state
    _doFill = True
    _doStroke = True
    _angleMode = DEGREES
    _rectMode = CORNER
    _circleMode = CENTER
    _state = STATE_STOP
    fill(255, 255, 255)
    stroke(0, 0, 0)
    global pre_globals, _main_loop_func

    _main_loop_func = None

    start_tag = "@loop_animation"
    end_tag = "@loop_animation"

    lines = src.split("\n")

    non_frame_code = []

    frame_code = []

    line_num = 0
    while line_num < len(lines):
        line = lines[line_num]
        line_num += 1
        if line.lower()[:len(start_tag)] != start_tag:
            non_frame_code.append(line)
        else:
            break

    while line_num < len(lines):
        line = lines[line_num]
        line_num += 1
        if line.lower()[:len(end_tag)] != end_tag:
            frame_code.append(" " + line +"\n")
        else:
            break

    while line_num < len(lines):
        line = lines[line_num]
        non_frame_code.append(line + "\n")
        line_num += 1

    src = "\n".join(non_frame_code)
    src += "\n"

    window.console.log("Non frame code:")
    window.console.log(src)

    if len(pre_globals) == 0:
        pre_globals = list(globals().keys())

    namespace = globals()
    namespace["__name__"] = "__main__"

    if len(frame_code) > 0:

        run_code(src, namespace, namespace, False)

        frame_code.insert(0, " saveState()")
        post_globals = list(globals().keys())
        global_code = ""
        for g in post_globals:
            if g not in pre_globals:
                global_code += g + ","
        if len(global_code) > 0:
            frame_code.insert(0, " global " + global_code[:-1])

        frame_code.insert(0, "def frame_code():")
        frame_code.insert(0, "@_loop")
        frame_code.append(" restoreState()")

        src = "\n".join(frame_code)
        window.console.log("Frame code:")
        window.console.log(src)

        run_code(src, namespace, namespace, True)
    else:
        run_code(src, namespace, namespace, True)

window.startSketch = startSketch

def run_code(src, globals, locals, is_frame_code = True):
    try:
        if is_frame_code:
            _start()

        exec(src, globals, locals)

    except Exception as e:
        do_print("Error in parsing: " + str(e) + "\n" + traceback.format_exc(), "red")
        _stop()

class ErrorOutput:
    def write(self, data):
        do_print(data, "red")
    def flush(self):
        pass

class PrintOutput:
    def write(self, data):
        do_print(data, "green")
    def flush(self):
        pass

sys.stdout = PrintOutput()
sys.stderr = ErrorOutput()
