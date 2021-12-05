import { runSkulpt, stopSkulpt, debugSkulpt } from './SkulptSetup'
import { Editor } from './EditorSetup'
import './editorLayout'
const Sk = require('skulpt')

// Only one session in playground
const session = 0

const editorWindow = document.getElementById('editor')
const crsfToken = 'no-token-needed-in-playground'
const sketchId = 0
const isReadOnly = false
const fileTabs = null

const aceEditor = new Editor(sketchId, crsfToken, Sk, fileTabs, isReadOnly)
Sk.PyAngelo.aceEditor = aceEditor
const listenForErrors = true
const autosave = false
aceEditor.onChange(listenForErrors, autosave)
aceEditor.listenForBreakPoints()

const startStopButton = document.getElementById('startStop')
startStopButton.addEventListener('click', runCode)
const stepIntoButton = document.getElementById('stepInto')
stepIntoButton.addEventListener('click', debugSkulpt)
const stepOverButton = document.getElementById('stepOver')
stepOverButton.addEventListener('click', debugSkulpt)
const slowMotionButton = document.getElementById('slowMotion')
slowMotionButton.addEventListener('click', debugSkulpt)
const continueButton = document.getElementById('continue')
continueButton.addEventListener('click', debugSkulpt)

function runCode () {
  startStopButton.removeEventListener('click', runCode, false)
  startStopButton.style.backgroundColor = '#880000'
  startStopButton.textContent = 'Stop'
  startStopButton.addEventListener('click', stopCode, false)
  Sk.PyAngelo.console.innerHTML = ''
  const debugging = document.getElementById('debug').checked
  runSkulpt(aceEditor.getCode(session), debugging, stopCode)
}

function stopCode () {
  stopSkulpt()
  startStopButton.removeEventListener('click', stopCode, false)
  startStopButton.style.backgroundColor = '#008800'
  startStopButton.textContent = 'Start'
  startStopButton.addEventListener('click', runCode, false)
}

const onresize = (domElem, callback) => {
  const resizeObserver = new ResizeObserver(() => callback())
  resizeObserver.observe(domElem)
}

onresize(editorWindow, function () {
  aceEditor.resize()
})

const snake = `from sprite import *
from random import *

eatSound = loadSound("/samples/sounds/powerup.wav")
gameOverSound = loadSound("/samples/sounds/collision.wav")

setCanvasSize(600, 600, JAVASCRIPT)
background(20, 20, 20)
welcome = TextSprite("Snake!", width/2, 150, fontSize=72)
welcome.setColour(255, 255, 0)
welcome.center()
startText = TextSprite("Press SPACE to start!", width/2, 450, fontSize=36)
startText.setColour(0, 255, 0)
startText.center()
keysText = TextSprite("Use WASD keys to move", width/2, 500, fontSize=14)
keysText.setColour(0, 255, 0)
keysText.center()
gameOverText = TextSprite("Game Over!", width/2, 150, fontSize=72)
gameOverText.setColour(255, 255, 0)
gameOverText.center()
scoreText = TextSprite("Score: 0000", width/2, 375, fontSize=72)
scoreText.setColour(0, 255, 0)
scoreText.center()
startAgainText = TextSprite("Press ENTER to try again!", width/2, 475, fontSize=36)
startAgainText.setColour(0, 255, 0)
startAgainText.center()
pyangelo = Sprite("/samples/images/PyAngelo.png", width/2 - 48, height/2 - 64)

# Set up the screen
BLOCK_WIDTH = 20
BLOCK_HEIGHT = 20
blocksX = width/BLOCK_WIDTH
blocksY = height/BLOCK_HEIGHT

INTRO = 1
PLAY = 2
GAMEOVER = 3
playing = True

def resetGame():
    global score, direction, snake, food
    score = 0
    direction = [1, 0]
    snake = [[4,5], [3,5], [2,5]]
    food = [10,10]

resetGame()
gameState = INTRO

while playing:
    if gameState == INTRO:
        background(50, 50, 50)
        welcome.draw()
        pyangelo.draw()
        startText.draw()
        keysText.draw()
        if isKeyPressed(KEY_SPACE):
            gameState = PLAY
    elif gameState == PLAY:
        background(25, 25, 25)
        score += 1
        if isKeyPressed("KeyA"):
            direction = [-1, 0]
        elif isKeyPressed("KeyD"):
            direction = [1, 0]
        elif isKeyPressed("KeyW"):
            direction = [0, -1]
        elif isKeyPressed("KeyS"):
            direction = [0, 1]

        # draw food
        fill(255, 255, 0)
        rect(food[0] * BLOCK_WIDTH, food[1] * BLOCK_HEIGHT, BLOCK_WIDTH, BLOCK_HEIGHT)

        # Draw the snake
        fill(0, 255, 0)
        stroke(0, 0, 0)
        for n, body in enumerate(snake):
            rect(body[0] * BLOCK_WIDTH, body[1] * BLOCK_HEIGHT, BLOCK_WIDTH, BLOCK_HEIGHT)
        # Move the snake
        snake.insert(0, [ snake[0][0] + direction[0], snake[0][1] + direction[1] ])
        
        # snake eats food
        if snake[0] == food:
            playSound(eatSound)
            # grow snake
            score += 100
            # generate new food
            # can't be located in the snake
            food = [randint(0, blocksX - 1), randint(0, blocksY - 1)]
            while food in snake:
                food = [randint(0, blocksX - 1), randint(0, blocksY - 1)]
        else: # did not eat any food so we don't grow
            snake.pop()
        
        # snake dies if it touches the edge
        if snake[0][0] < 0 or snake[0][1] < 0 or snake[0][0] >= blocksX or snake[0][1] >= blocksY or snake[0] in snake[1:]: 
            gameState = GAMEOVER
            playSound(gameOverSound)
        # show score 
        stroke(0, 255, 0)
        noFill()
        rect(150, 30, width - 300, 30)
        fill(0, 255, 0)
        text("Score: "  + str(score), 275, 37, fontSize=15)
        sleep(0.1)
    elif gameState == GAMEOVER:
        background(50, 50, 50)
        gameOverText.draw()
        pyangelo.draw()
        scoreText.text = "Score: " + str(score)
        scoreText.draw()
        startAgainText.draw()
        if isKeyPressed(KEY_ENTER):
            gameState = PLAY
            resetGame()
        elif isKeyPressed(KEY_Q):
            playing = False
`

const breakout = `from sprite import *
from random import *
import time

setCanvasSize(960, 540, JAVASCRIPT)

ORIG_PADDLE_WIDTH = 240
PADDLE_SPEED = 500
BALL_START_SPEED_Y = 300
hitPaddle = loadSound("/samples/sounds/blip.wav")
hitBrick = loadSound("/samples/sounds/hit.wav")
hitWall = loadSound("/samples/sounds/hit3.wav")
hitBottom = loadSound("/samples/sounds/collision.wav")

hiScore = 0

class Particle():
    def __init__(self, x, y, r, g, b):
        self.x = x
        self.y = y
        self.r = r
        self.g = g
        self.b = b
        self.dx = uniform(-1, 1)
        self.dy = uniform(-1, -1)
        self.accX = uniform(-0.2, 0.2)
        self.accY = uniform(-0.2, 0.2)
        self.radius = 2
        self.alpha = 1.0
    def update(self):
        self.x += self.dx
        self.y += self.dy
        self.dx += self.accX
        self.dy += self.accY
        self.alpha -= 0.02
    def draw(self):
        noStroke()
        fill(self.r, self.g, self.b, self.alpha)
        circle(self.x, self.y, self.radius)
BRICK_WIDTH = 60
BRICK_HEIGHT = 20
SPACING = 2
TOP = 100
class Brick(RectangleSprite):
    def __init__(self, row, col):
        self.col = col
        x = col * BRICK_WIDTH + SPACING
        y = row * BRICK_HEIGHT + SPACING + TOP
        if row < 2:
            r = 255
            g = 0
            b = 0
            self.score = 7
        elif row < 4:
            r = 255
            g = 165
            b = 0
            self.score = 5
        elif row < 6:
            r = 0
            g = 255
            b = 0
            self.score = 3
        elif row < 8:
            r = 255
            g = 255
            b = 0
            self.score = 1
        super().__init__(x, y, BRICK_WIDTH - SPACING * 2, BRICK_HEIGHT - SPACING * 2, r, g, b)
        self.visible = True

paddle = RectangleSprite(width/2 - ORIG_PADDLE_WIDTH/2, 510, ORIG_PADDLE_WIDTH, 20, 0, 0, 255)
ball = CircleSprite(width/2 - 5, 300, 10, 255, 255, 255)
ball.dx = uniform(-100, 100)
ball.dy = BALL_START_SPEED_Y

breakoutText = TextSprite("Breakout!", width/2, height/2, fontSize = 72)
breakoutText.center()
breakoutText.setColour(220, 220, 220)
startText = TextSprite("Press SPACE to start", width/2, height/2 + 100, fontSize = 24)
startText.center()
startText.setColour(0, 255, 0)
hiScoreText = TextSprite("Hi Score: 0", width/2, height/2 + 200, fontSize = 24)
hiScoreText.center()
hiScoreText.setColour(255, 255, 0)

gameOverText = TextSprite("Game Over!", width/2, height/2 + 100, fontSize = 72)
gameOverText.center()
gameOverText.setColour(220, 220, 220)
restartText = TextSprite("Press ENTER to restart", width/2, height/2 + 200, fontSize = 24)
restartText.center()
restartText.setColour(0, 255, 0)

while True:
    intro = True
    while intro:
        background(0, 0, 0)
        fill(220, 220, 220)
        breakoutText.draw()
        startText.draw()
        hiScoreText.draw()
        if isKeyPressed(KEY_SPACE):
            intro = False
        sleep(0.005)

    paddle.width = ORIG_PADDLE_WIDTH
    paddle.moveTo(width/2 - ORIG_PADDLE_WIDTH/2, 510)
    paddleNotChanged1 = True
    paddleNotChanged2 = True
    ball.moveTo(width/2 - 5, 300)
    ball.dx = uniform(-100, 100)
    ball.dy = BALL_START_SPEED_Y
    # Add bricks
    bricks = []
    for i in range(8):
        brickRow = []
        for j in range(int(width/BRICK_WIDTH)):
            brickRow.append(Brick(i, j))
        bricks.append(brickRow)
    # List for brick explosion
    particles = []
    score = 0
    
    lastFrameTime = time.time()
    playing = True
    while playing:
        currentTime = time.time()
        dt = currentTime - lastFrameTime
        lastFrameTime = currentTime

        background(0, 0, 0)
        stroke(0, 0, 0)
        fill(220, 220, 220)
        text("Score: " + str(score), width - 175, 20, fontSize = 30)
        if dt > 0:
            text("FPS: " + str(int(1/dt)), 0, 0)
        if paddleNotChanged1 and score >= 20:
            paddle.width = ORIG_PADDLE_WIDTH / 2
            paddle.x += paddle.width / 2
            paddleNotChanged1 = False
        elif paddleNotChanged2 and score >= 300:
            paddle.width = ORIG_PADDLE_WIDTH / 4
            paddleNotChanged2 = False
            paddle.x += paddle.width / 2
            
        for brickRow in bricks:
            for brick in brickRow:
                if brick.visible:
                    brick.draw()
                    if brick.overlaps(ball):
                        for i in range(30):
                            particles.append(Particle(brick.x + brick.width/2, brick.y + brick.height/2, brick.r, brick.g, brick.b))
                        playSound(hitBrick)
                        score += brick.score
                        brick.visible = False
                        if ball.dy >= 0 and ball.y > brick.y:
                            ball.dx *= -1
                        elif ball.dy < 0 and ball.y < brick.y + brick.height:
                            ball.dx *= -1
                        else:
                            ball.dy *= -1
                        break
        
        ball.draw()
        paddle.draw()
        
        ball.moveBy(ball.dx * dt, ball.dy * dt)
        
        if ball.x + ball.radius > width:
            playSound(hitWall)
            ball.x = width - ball.radius
            ball.dx *= -1
        if ball.x - ball.radius <= 0:
            playSound(hitWall)
            ball.x = ball.radius
            ball.dx *= -1
        if ball.y - ball.radius <= 0:
            playSound(hitWall)
            ball.y = ball.radius
            ball.dy *= -1
        if ball.y > height:
            playSound(hitBottom)
            playing = False
            
        if isKeyPressed(KEY_A):
            paddle.moveBy(-PADDLE_SPEED * dt, 0)
        if isKeyPressed(KEY_D):
            paddle.moveBy(PADDLE_SPEED * dt, 0)
        
        if paddle.overlaps(ball) and ball.y <= paddle.y:
            playSound(hitPaddle)
            ball.y = paddle.y - ball.radius
            ball.dy *= -1
            ball.dx += uniform(-150, 150)
            # Hit left third of paddle
            if ball.x < paddle.x + paddle.width/3 :
                ball.dx -= uniform(100, 180)
            # Hit right third of paddle
            elif ball.x > paddle.x + 2*paddle.width/3:
                ball.dx += uniform(100, 180)
                
        for i in range(len(particles) - 1, -1, -1):
            particles[i].draw()
            particles[i].update()
            if particles[i].alpha <= 0:
                del particles[i]
        
        sleep(0)
    
    gameOverText.draw()
    restartText.draw()
    if score > hiScore:
        hiScore = score
        hiScoreText.text = "Hi Score: " + str(hiScore)
    gameOver = True
    while gameOver:
        if isKeyPressed(KEY_ENTER):
            gameOver = False
`

const randomCircles = `from random import *
setCanvasSize(640, 360, JAVASCRIPT)
background(0, 0, 0)
#noStroke()
while True:
    r = randint(100, 200)
    g = randint(0, 20)
    b = randint(100, 200)
    x = randint(0, width)
    y = randint(0, height)
    size = randint(5, 30)
    fill(r, g, b, 0.7)
    circle(x, y, size)
    sleep(0.05)
`

const blankEditor = ''

const snakeBtn = document.getElementById('snakeBtn')
snakeBtn.onclick = function () { aceEditor.replaceSession(session, snake) }
const breakoutBtn = document.getElementById('breakoutBtn')
breakoutBtn.onclick = function () { aceEditor.replaceSession(session, breakout) }
const randomCirclesBtn = document.getElementById('randomCirclesBtn')
randomCirclesBtn.onclick = function () { aceEditor.replaceSession(session, randomCircles) }
const blankEditorBtn = document.getElementById('blankEditorBtn')
blankEditorBtn.onclick = function () { aceEditor.replaceSession(session, blankEditor) }

aceEditor.addSession(snake)
aceEditor.setSession(session)
