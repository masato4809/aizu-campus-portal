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

interface IProps extends IPropsTypography {}
export const TypoH3: React.FC<IProps> = ({
  sx,
  children,
  bold,
  strike,
  noWrap,
  size,
  lineHeight,
  color,
  align,
  component = 'h3',
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
      variant="h3"
      component={component as React.ElementType}
      noWrap={noWrap}
      align={align}
    >
      {children}
    </Typography>
  );
};
