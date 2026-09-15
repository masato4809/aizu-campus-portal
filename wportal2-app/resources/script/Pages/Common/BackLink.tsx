import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { E_COLOR } from '@/script/Enum/EColor';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import {
  EPages,
  getPagesBack,
  getPagesHref,
  getPagesName,
  IPagesHrefReplace,
} from '@/script/Enum/Server/App/EPages';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_ICON } from '@/script/Enum/EIcon';

interface IProps extends IPropsBase {
  current: EPages;
  replace?: IPagesHrefReplace[];
}
export const BackLink: React.FC<IProps> = ({ current, replace = [] }) => {
  const backList = getPagesBack(current);
  return (
    <Box
      sx={{
        display: 'flex',
        alignItems: 'center',
        marginBottom: responsiveSize(8),
        gap: responsiveSpacing(2),
      }}
    >
      {backList.map(back => {
        return (
          <React.Fragment key={back}>
            <Icon icon={E_ICON.ARROW_BACK} />
            <InertiaLink href={getPagesHref(back, replace)}>
              <TypoText
                sx={{
                  textDecoration: 'underline',
                  marginRight: responsiveSpacing(4),
                  '&:hover': {
                    color: E_COLOR.PRIMARY_DARK,
                  },
                }}
              >
                {getPagesName(back)}
              </TypoText>
            </InertiaLink>
          </React.Fragment>
        );
      })}
    </Box>
  );
};
