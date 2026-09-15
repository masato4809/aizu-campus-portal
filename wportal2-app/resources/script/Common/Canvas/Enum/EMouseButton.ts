/**
 * マウスのボタン判定.
 */
export const E_MOUSE_BUTTON = {
  LEFT: 0,
  MIDDLE: 1,
  RIGHT: 2,
} as const;
export type EMouseButton = (typeof E_MOUSE_BUTTON)[keyof typeof E_MOUSE_BUTTON];
