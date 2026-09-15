import * as React from 'react';
import { Badge, Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { Icon } from '@/script/Component/Misc/Icon';
import { EIcon } from '@/script/Enum/EIcon';
import { E_COLOR } from '@/script/Enum/EColor';

interface IProps extends IPropsBase {
  href: string;
  icon: EIcon;
  label: string;
  target?: string;
  badgeCount?: number;
}
export const MobileIcon: React.FC<IProps> = ({
  sx,
  href,
  icon,
  label,
  target,
  badgeCount,
}) => {
  const nodeIcon = (): React.ReactNode => {
    return (
      <>
        <Box
          sx={{
            width: '80px',
            height: '80px',
            borderRadius: '8px',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            backgroundColor: E_COLOR.PRIMARY_MAIN,
          }}
        >
          <Badge badgeContent={badgeCount} color="secondary">
            <Icon
              sx={{
                width: '60px',
                height: '60px',
                color: E_COLOR.WHITE,
              }}
              icon={icon}
            />
          </Badge>
        </Box>
        <TypoText sx={{ marginTop: '4px' }} align="center">
          {label}
        </TypoText>
      </>
    );
  };

  if (target) {
    return (
      <a href={href} target={target} rel="noreferrer">
        <Box
          sx={{
            width: '100px',
            padding: '10px',
            ...sx,
          }}
        >
          {nodeIcon()}
        </Box>
      </a>
    );
  }

  return (
    <InertiaLink
      sx={{
        width: '100px',
        padding: '10px',
        ...sx,
      }}
      href={href}
    >
      {nodeIcon()}
    </InertiaLink>
  );
};
