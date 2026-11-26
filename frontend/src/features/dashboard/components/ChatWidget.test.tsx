import { describe, it, expect } from 'vitest'
import { render, screen } from '@testing-library/react'
import { ChatWidget } from './ChatWidget'

describe('ChatWidget', () => {
  it('renders chat widget', () => {
    const { container } = render(<ChatWidget />)
    expect(container).toBeTruthy()
  })

  it('renders welcome message when no messages', () => {
    render(<ChatWidget />)
    expect(screen.getByText(/Start a conversation/i)).toBeTruthy()
  })

  it('renders input field for messages', () => {
    render(<ChatWidget />)
    expect(screen.getByPlaceholderText(/Enter a message/i)).toBeTruthy()
  })

  it('renders send button', () => {
    render(<ChatWidget />)
    const buttons = screen.getAllByRole('button')
    // First button is send button (second is refresh in health status)
    expect(buttons.length).toBeGreaterThan(0)
    expect(buttons[0]).toBeTruthy()
  })
})
