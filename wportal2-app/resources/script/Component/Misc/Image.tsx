import * as React from 'react';
import { styled } from '@mui/material';
import { IPropsBase } from '@/script/System/System';

const StyledImage = styled('img')({});

interface IProps extends IPropsBase {
  alt?: string;
  src: string;
}
export const Image: React.FC<IProps> = ({ sx, alt, src }) => {
  return (
    <StyledImage
      sx={{
        ...sx,
      }}
      alt={alt}
      src={src}
    />
  );
};
