import React from 'react';
import { TypographyOwnProps } from '@mui/material';
import { IPropsBase } from '@/script/System/System';

// TypographyOwnPropsから利用するキー.
type UseTypographyOwnPropsKeys = 'align';

export interface IPropsTypography
  extends IPropsBase,
    Pick<TypographyOwnProps, UseTypographyOwnPropsKeys> {
  bold?: boolean;
  strike?: boolean;
  color?: string;
  size?: string;
  lineHeight?: string;
  noWrap?: boolean;
  component?: React.ElementType;
}

/**
 * テキストの性質を一括で変更する場合は下記を修正する.
 */

/**
 * font-size
 * @param size
 */
export const fontSize = (size?: string): string | undefined => {
  return size ?? undefined;
};

/**
 * line-height
 * @param lineHeight
 */
export const fontLineHeight = (lineHeight?: string): string | undefined => {
  return lineHeight ?? undefined;
};

/**
 * font-weight
 * @param bold
 */
export const fontWeight = (bold?: boolean): string => {
  return bold ? 'bold' : 'normal';
};

/**
 * color
 * @param color
 */
export const fontColor = (color?: string): string | undefined => {
  return color ?? undefined;
};

/**
 * text-decoration-line
 * @param strike
 */
export const textDecorationLine = (strike?: boolean): string => {
  return strike ? 'line-through' : 'none';
};
