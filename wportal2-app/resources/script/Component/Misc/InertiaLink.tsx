import React from 'react';
import { Box } from '@mui/material';
import { InertiaLinkProps, Link } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { useProgressContext } from '@/script/Provider/ProgressProvider';

interface IProps extends IPropsBase, InertiaLinkProps {}
export const InertiaLink: React.FC<IProps> = ({
  sx,
  children,
  as,
  href,
  data,
  method,
  headers,
  replace,
  onStart,
  onFinish,
}) => {
  const progressContext = useProgressContext();

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <Link
        as={as}
        href={href}
        data={data}
        method={method}
        headers={headers}
        replace={replace}
        onStart={onStart ?? progressContext.onStart}
        onFinish={onFinish ?? progressContext.onFinish}
      >
        {children}
      </Link>
    </Box>
  );
};
