import sys
import time
import traceback
import javascript
import random
import json
import copy
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
    def __init__(self, image, x = 0, y = 0, width = 0, height = 0, a = 1.0):
        self.x = x
        self.y = y
        self.width = width
        self.height = height
        self.opacity = a
        self.image = canvas.loadImage(image, self)
        # user defined
        self.type = 0

    def draw(self, offsetX = 0, offsetY = 0):
        canvas.drawImage(self.image, self.x - offsetX, self.y - offsetY, self.width, self.height, opacity=self.opacity)

    def moveBy(self, x, y):
        self.x += x
        self.y += y

    def moveTo(self, x, y):
        self.x = x
        self.y = y

    def overlaps(self, other):
        # TODO: BUG! If the 'other' is an image that has a shared URL with a previously loaded image, collision doesn't work!!
        return ((self.x < (other.x + other.width)) and ((self.x + self.width) > other.x) and (self.y < (other.y + other.height)) and ((self.y + self.height) > other.y))

    def contains(self, point):
        return point.x >= self.x and point.x <= self.x + self.width and point.y >= self.y and point.y <= self.y + self.height

class TextSprite(Sprite):
    def __init__(self, text, x = 0, y = 0, fontName = "Arial", fontSize = 20, r = 255, g = 255, b = 255, a = 1.0):
        self.text = text
        self.x = x
        self.y = y
        self.fontName = fontName
        self.fontSize = fontSize
        self.setColour(r, g, b)
        # Get Text Width
        canvas.ctx.font = str(fontSize) + "pt " + self.fontName
        textMetrics = canvas.ctx.measureText(self.text)
        self.width = abs(textMetrics.actualBoundingBoxLeft) + abs(textMetrics.actualBoundingBoxRight)
        self.height = abs(textMetrics.actualBoundingBoxAscent) + abs(textMetrics.actualBoundingBoxDescent)
        # user defined
        self.type = 0

    def center(self):
        self.x -= (self.width/2)
        self.y -= (self.height/2)

    def draw(self, offsetX = 0, offsetY = 0):
        canvas.drawText(self.text, self.x - offsetX, self.y - offsetY, self.fontName, self.fontSize, self.r, self.g, self.b, self.a)

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
        # user defined
        self.type = 0

    def draw(self, offsetX = 0, offsetY = 0):
       canvas.drawRect(self.x - offsetX, self.y - offsetY, self.width, self.height, self.r, self.g, self.b, self.a)

class CircleSprite(TextSprite):
    def __init__(self, x, y, radius, r = 255, b = 255, g = 255, a = 1.0):
        # Set to enable standard collision detection in overlaps method
        self.x = x - radius
        self.y = y - radius
        self.radius = radius
        self.width = radius * 2
        self.height = radius * 2
        self.setColour(r, g, b, a)
        # user defined
        self.type = 0

    def draw(self, offsetX = 0, offsetY = 0):
        canvas.drawCircle(self.x + self.radius - offsetX, self.y + self.radius - offsetY, self.radius, self.r, self.g, self.b, self.a)

class PyAngelo():
    STATE_STOP      =   1
    STATE_RUN       =   2
    STATE_HALT      =   3
    STATE_LOAD      =   4
    STATE_INPUT     =   5
    STATE_LOADED    =   6
    DEFAULT_WIDTH   = 500
    DEFAULT_HEIGHT  = 400
    
    def __init__(self):
       
        self.commands = []
        
        # get the canvas element
        self.canvas = document["canvas"]
        self.ctx = self.canvas.getContext('2d')		
        self.setSize(self.DEFAULT_WIDTH, self.DEFAULT_HEIGHT);

        self.mousePressed = False;
        self.mouseX = 0
        self.mouseY = 0
        self.canvas.bind("mousedown", self._mousedown)
        self.canvas.bind("mouseup", self._mouseup)
        self.canvas.bind("mousemove", self._mousemove)
        
        self.timer_id = None
        self.main_loop = None
        
        self.stopped = False
        
        self.resources =  {}
        self.loadingResources = 0
        
        self.keys = dict([(a, False) for a in range(255)] +
                         [(a, False) for a in range(0xff00, 0xffff)]) 
        self.keys[KEY_V_LEFT] = False
        self.keys[KEY_V_RIGHT] = False
        self.keys[KEY_V_UP] = False
        self.keys[KEY_V_DOWN] = False
        self.keys[KEY_V_FIRE] = False                               

        document.bind("keydown", self._keydown)
        document.bind("keyup", self._keyup)   
        
        self.soundPlayers = {}        
        
        self.state = self.STATE_STOP
        self.anim_timer = 0
        self.anim_time = 200        
        
        self.starting_text = "Starting up"   
        self.loading_text = "Loading resources"        
        
        # set background to cornflower blue (XNA!) by default
        self.background(100, 149, 237)
        
        self.pixel_id = self.ctx.createImageData(1, 1)
        
        self.last_frame_commands = []
        
        self.just_halted = False
        
        self.input_concluded = False
        
        self.input_buffer_index = 0
        
        self.loading_filename = ""
        
        timer.request_animation_frame(self.update)     

    def setSize(self, width, height):
        self.canvas["width"] = width;
        self.canvas["height"] = height;
        self.width = self.canvas.width
        self.height = self.canvas.height

    def isKeyPressed(self, key):
        return self.keys[key]             

    def isMousePressed(self):
        return self.mousePressed
        
    def refresh(self):
        self.execute_commands()
    
    ########################################################################################
        
    def loadSound(self, filename, loop = False, streaming = False):
        howl = window.Howl
        sound = howl.new({"src": [filename], "loop": loop, "onload": self._soundLoaded})
        self.loadingResources += 1

        self.soundPlayers[filename] = sound
        return filename
        
    def _soundLoaded(self, e, f):
        self.loadingResources -= 1

    def playSound(self, sound, loop = False, volume = 1.0):
        if sound not in self.soundPlayers:
            sound = self.loadSound(sound)
            
        self.soundPlayers[sound].loop(loop)
        self.soundPlayers[sound].volume(volume)        
        self.soundPlayers[sound].play()
            
            
    def stopAllSounds(self):
        for sound in self.soundPlayers:
            self.stopSound(sound)

    def pauseSound(self, sound):
        if sound in self.soundPlayers:
            self.soundPlayers[sound].pause()       

    # alias for pauseSound
    def stopSound(self, sound):
        if sound in self.soundPlayers:
            self.soundPlayers[sound].stop()   
        
    def _keydown(self, ev):
       
        self.keys[ev.which] = True
               
        # pressing escape when the program has halted
        if ev.which == KEY_ESC and self.state == self.STATE_HALT:
            self.stop()
            
        # TODO: support stopping during INPUT state
            
        if self.state == self.STATE_INPUT:
            if ev.which != KEY_ENTER and (ev.which < 32 or ev.which > KEY_A + 26):
                return
            array[len(array) - 1 - self.input_buffer_index] = ev.which
            if ev.which == KEY_ENTER:
                self.input_concluded = True
            else:
                returned_string = ""
                n = self.input_buffer_index
                while n >= 0:
                
                    returned_string = chr(array[len(array) - 1 - n]) + returned_string
                    n -= 1
                
                self.input_buffer_index += 1
                self.drawText(returned_string + "_", 0, 0)

    def _keyup(self, ev):
        self.keys[ev.which] = False  

    def setMousePosition(self, ev):
        self.boundingRect = self.canvas.getBoundingClientRect()
        self.mouseX = int(ev.clientX - self.boundingRect.left)
        self.mouseY = int(self.height - (ev.clientY - self.boundingRect.top))

    def _mousemove(self, ev):
        self.setMousePosition(ev)
        
    def _mousedown(self, ev):
        self.setMousePosition(ev)
        self.mousePressed = True
        
    def _mouseup(self, ev):
        self.setMousePosition(ev)
        self.mousePressed = False
    
    def resourceError(self, e):
        self.stop()
        do_print("Error loading of resource: " + e.target.src + "\n", "red")
        #del e.target
        #e.target.parentElement.removeChild(e.target)
    
    def resourceAbort(self, e):
        self.stop()
        do_print("Aborted loading of resource: " + e.target.src + "\n", "red")  
    
    def resourceLoaded(self, e):
        window.console.log("Successfully loaded file:" + e.target.src);
            
        e.target.pyangeloImage.height = e.target.naturalHeight
        e.target.pyangeloImage.width = e.target.naturalWidth

        if e.target.pyangeloImage.sprite is not None:
            if e.target.pyangeloImage.sprite.height == 0:
                e.target.pyangeloImage.sprite.height = e.target.naturalHeight
            if e.target.pyangeloImage.sprite.width == 0:
                e.target.pyangeloImage.sprite.width = e.target.naturalWidth
            
        self.loadingResources -= 1
            
    def loadImage(self, file, sprite = None):
        self.loadingResources += 1
        
        window.console.log("Attempting to load file:" + file);
        img = html.IMG()
        img.crossOrigin = "Anonymous"
        img.src = file

        img.bind('load', self.resourceLoaded)
        img.bind('error', self.resourceError)
        img.bind('abort', self.resourceAbort)
        
        pyangeloImage = PyAngeloImage(img, sprite)
        img.pyangeloImage = pyangeloImage

        return pyangeloImage

    def drawImage(self, image, x, y, width = None, height = None, rotation=0, anchorX = None, anchorY = None, opacity=None):
        if (isinstance(image, str)):
            image = self.loadImage(image)
                   
        self.ctx.save()

        if width is None:
            width = image.width

        if height is None:
            height = image.height

        if opacity is not None:
            if opacity > 1.0:
                opacity = 1.0
            elif opacity < 0.0:
                opacity = 0.0
            self.ctx.globalAlpha = opacity

        if rotation != 0.0:
            # TODO: Buggy!!!
            self.ctx.save()
            self.ctx.translate(x, self._convY(y))
            self.ctx.rotate(- rotation)# - 3.1415926535)# + math.PI / 180)
            self.ctx.drawImage(image.img, -anchorX * width, -anchorY * height, width, height)
            self.ctx.restore()
        else:
            self.ctx.drawImage(image.img, x, self._convY(y + height), width, height)

        self.ctx.restore()    
        
    def measureText(self, text, fontName = "Arial", fontSize = 10):
        self.ctx.font = str(fontSize) + "pt " + fontName
        textMetrics = self.ctx.measureText(text)

        return (abs(textMetrics.actualBoundingBoxLeft) + abs(textMetrics.actualBoundingBoxRight), abs(textMetrics.actualBoundingBoxAscent) + abs(textMetrics.actualBoundingBoxDescent))

    def drawText(self, text, x, y, fontName = "Arial", fontSize = 10, r = 255, g = 255, b = 255, a = 1.0):
        self.ctx.fillStyle = "rgba(" + str(r) + "," + str(g) + "," + str(b) + "," + str(a) + ")"
        self.ctx.font = str(fontSize) + "pt " + fontName
        self.ctx.textBaseline = "bottom"
        self.ctx.fillText(text, x, self.height - y)        

    def background(self, r = 0, g = 0, b = 0, a = 1):
        global array
        self.ctx.fillStyle= "rgba(" + str(r) + "," + str(g) + "," + str(b) + "," + str(a)+ ")"
        self.ctx.fillRect(0, 0, self.width, self.height)    
        
    def drawLine(self, x1, y1, x2, y2, r = 255, g = 255, b = 255, a = 1.0, width = 1):
        r = min(r, 255)
        g = min(g, 255)
        b = min(b, 255)
        a = min(a, 1.0)

        self.ctx.beginPath()
        self.ctx.lineWidth = width
        self.ctx.strokeStyle = "rgba(" + str(r) + "," + str(g) + "," + str(b) + "," + str(a) + ")"
        self.ctx.moveTo(x1, self._convY(y1))
        self.ctx.lineTo(x2, self._convY(y2))
        self.ctx.stroke()

    def drawCircle(self, x, y, radius, r=255, g=255, b=255, a=1.0):
        r = min(r, 255)
        g = min(g, 255)
        b = min(b, 255)
        a = min(a, 1.0)

        self.ctx.fillStyle = "rgba(" + str(r) + "," + str(g) + "," + str(b) + "," + str(a) + ")"
        self.ctx.beginPath();
        self.ctx.arc(x, self._convY(y), radius, 0, 2 * 3.1415926535);
        self.ctx.fill();
        
    def drawPixel(self, x, y, r = 255, g = 255, b = 255, a = 255):
        r = min(r, 255)
        g = min(g, 255)
        b = min(b, 255)
        a = min(a, 255)

        self.pixel_id.data[0] = r
        self.pixel_id.data[1] = g
        self.pixel_id.data[2] = b
        self.pixel_id.data[3] = a
        self.ctx.putImageData(self.pixel_id, x, self._convY(y))
        
    def drawRect(self, x, y, w, h, r = 255, g = 255, b = 255, a = 1.0):
        r = min(r, 255)
        g = min(g, 255)
        b = min(b, 255)
        a = min(a, 1.0)
        self.ctx.fillStyle = "rgba(" + str(r) + "," + str(g) + "," + str(b) + "," + str(a) + ")"
        self.ctx.fillRect(x, self._convY(y) - h, w, h);
        
    def _convY(self, y):
        return self.height - y

    def __input(self, msg):
        # input mode triggered
        self.state = self.STATE_INPUT
        self.input_concluded = False
        self.input_buffer_index = 0
        
    def loop(self, func):
        self.main_loop = func

        
    def update(self, deltaTime):           
        if self.state == self.STATE_STOP:      
            self.background(100, 149, 237)
            ready = TextSprite("Ready", canvas.width/2, canvas.height/2, fontSize = 60)
            ready.center()
            ready.draw()
        elif self.state == self.STATE_RUN:   
            if self.main_loop is not None:
                try:
                    self.main_loop()
                except Exception as e:
                    do_print("Error: " + str(e) + "\n" + traceback.format_exc(), "red")       
                    self.stop()
            	   
        elif self.state == self.STATE_INPUT:
            # display the commands in the queue to date
            if self.input_concluded:
                # TODO: if the program halts after the input, then this causes
                # Pyangelo to keep looping in it's run state ===> FIX!
                self.state = self.STATE_RUN
                self.input_concluded = False
        elif self.state == self.STATE_LOAD:
            self.background(49, 98, 186)
            loading = TextSprite("Loading: " + self.loading_filename, canvas.width/2, canvas.height/2, fontSize = 20)
            loading.center()
            loading.draw()
        elif self.state == self.STATE_LOADED:
            self.background(49, 98, 186)
            
            loaded = TextSprite("Successfully Loaded:", canvas.width/2, canvas.height/2 + 50, fontSize = 20)
            loaded.center()
            loaded.draw()
            
            loadedFilename = TextSprite(self.loading_filename, canvas.width/2, canvas.height/2 - 50, fontSize = 20)
            loadedFilename.center()
            loadedFilename.draw()
        
        timer.request_animation_frame(self.update)
        
    def start(self):
        if self.state != self.STATE_RUN:
            self.state = self.STATE_RUN
                        
    def stop(self):   
        if self.state != self.STATE_STOP:
            self.state = self.STATE_STOP            

            # TODO: put all these into a Reset() method
            self.resources =  {}
            self.loadingResources = 0

            self.stopAllSounds()     

    def getPixelColour(self, x, y):
        pixel = window.Int8Array.new(4)      
                   
        imageData = self.ctx.getImageData(x, self._convY(y), 1, 1)
        
        return Colour(imageData.data[0], imageData.data[1], imageData.data[2], imageData.data[3])
              

    def sleep(self, milliseconds):
        # the sleep happens here, it's a tight loop - may hang the browser!
        currTime = window.performance.now()
        prevTime = currTime
        while (currTime - prevTime < milliseconds):
            currTime = window.performance.now()      
            
canvas = PyAngelo()

def canvas_stop():
    canvas.stop()

window.canvas_stop = canvas_stop

def startLoading(filename):
    window.console.log("starting load")
    canvas.state = canvas.STATE_LOAD
    canvas.loading_filename = filename
    
def doneLoading():
    window.console.log("finish load")
    canvas.state = canvas.STATE_LOADED
    
    canvas.resources =  {}
    canvas.loadingResources = 0
    canvas.stopAllSounds()

def format_string_HTML(s):
    return s.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;").replace("\n", "<br>").replace("\"", "&quot;").replace("'", "&apos;").replace(" ", "&nbsp;")

def do_print(s, color=None):
    if color is not None:
        window.writeOutput("<p style='display:inline;color:" + color + ";'>" + format_string_HTML(s) + "</p>", True)
    else:
        window.writeOutput("<p style='display:inline;'>" + format_string_HTML(s) + "</p>", True)


pre_globals = []
def startSketch(src):
    global pre_globals
    
    canvas.main_loop = None

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
        
        post_globals = list(globals().keys())
        global_code = ""
        for g in post_globals:
            if g not in pre_globals:
                global_code += g + ","
        if len(global_code) > 0:
            frame_code.insert(0, " global " + global_code[:-1] + "\n")
        
        frame_code.insert(0, "def frame_code():")
        
        frame_code.insert(0, "@canvas.loop")
        
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
            canvas.start()

        exec(src, globals, locals)
        
    except Exception as e:
        do_print("Error in parsing: " + str(e) + "\n" + traceback.format_exc(), "red") 
        canvas.stop()

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
        
###################################################################################        
