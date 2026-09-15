import * as React from 'react';
import { Typography } from '@mui/material';
import {
  fontColor,
  fontLineHeight,
  fontSize,
  fontWeight,
  IPropsTypography,
  textDecorationLine,
} from '@/script/Component/Typography/IPropsTypo';
import { E_COLOR } from '@/script/Enum/EColor';

interface IProps extends IPropsTypography {}
export const TypoError: React.FC<IProps> = ({
  sx,
  children,
  bold,
  strike,
  noWrap,
  size,
  lineHeight,
  color = E_COLOR.TEXT_RED,
  align,
  component = 'p',
}) => {
  return (
    <Typography
      sx={{
        fontSize: fontSize(size),
        fontWeight: fontWeight(bold),
        lineHeight: fontLineHeight(lineHeight),
        color: fontColor(color),
        textDecorationLine: textDecorationLine(strike),
        ...sx,
      }}
      variant="body1"
      component={component as React.ElementType}
      noWrap={noWrap}
      align={align}
    >
      {children}
    </Typography>
  );
};
