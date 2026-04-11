/**
 * @jest-environment jsdom
 */
import { buildImagePreview, buildAudioPreview } from './EditorSetup'

describe('buildImagePreview', () => {
  it('returns a div containing an img with the correct src', () => {
    const filename = 'photo.jpg'
    const el = buildImagePreview(filename)
    expect(el.tagName).toBe('DIV')
    const img = el.querySelector('img')
    expect(img).not.toBeNull()
    expect(img.getAttribute('src')).toBe(filename)
  })

  it('does not execute XSS payloads — filename is used as attribute value, not HTML', () => {
    const xss = '"><script>alert(1)</script><img src="'
    const el = buildImagePreview(xss)
    const img = el.querySelector('img')
    expect(img).not.toBeNull()
    // The literal string is stored as the src attribute value, not parsed as markup
    expect(img.getAttribute('src')).toBe(xss)
    // No injected script elements inside the container
    expect(el.querySelectorAll('script')).toHaveLength(0)
  })
})

describe('buildAudioPreview', () => {
  it('returns a figure with a figcaption and audio element', () => {
    const filename = 'sound.mp3'
    const el = buildAudioPreview(filename)
    expect(el.tagName).toBe('FIGURE')
    const figcaption = el.querySelector('figcaption')
    expect(figcaption).not.toBeNull()
    expect(figcaption.textContent).toBe(filename)
    const audio = el.querySelector('audio')
    expect(audio).not.toBeNull()
    expect(audio.getAttribute('src')).toBe(filename)
    expect(audio.getAttribute('preload')).toBe('none')
    expect(audio.hasAttribute('controls')).toBe(true)
  })

  it('does not execute XSS payloads in the figcaption', () => {
    const xss = '<img src=x onerror=alert(1)>'
    const el = buildAudioPreview(xss)
    const figcaption = el.querySelector('figcaption')
    // textContent stores literal string — no child elements
    expect(figcaption.textContent).toBe(xss)
    expect(figcaption.children).toHaveLength(0)
  })

  it('does not execute XSS payloads in the audio src', () => {
    const xss = '"><script>alert(1)</script><audio src="'
    const el = buildAudioPreview(xss)
    const audio = el.querySelector('audio')
    expect(audio.getAttribute('src')).toBe(xss)
    expect(el.querySelectorAll('script')).toHaveLength(0)
  })
})
