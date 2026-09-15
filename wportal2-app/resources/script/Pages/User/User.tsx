import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import {
  E_PAGES,
  getPagesHref,
  getPagesName,
} from '@/script/Enum/Server/App/EPages';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { UserList } from '@/script/Pages/User/UserList';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { InertiaLink } from '@/script/Component/Misc/InertiaLink';
import { useCommonIndexContext } from '@/script/Provider/CommonIndexProvider';
import { Closable } from '@/script/Component/Misc/Closable';
import { E_USER_AUTHORITY } from '@/script/Enum/Server/App/EUserAuthority';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const User: React.FC<IProps> = () => {
  const { trnUserAuthorityList } = useCommonIndexContext();
  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>{getPagesName(E_PAGES.USER)}</TypoH1>
        <Closable
          open={trnUserAuthorityList.hasAuthority([
            E_USER_AUTHORITY.ADMIN_PRIVILEGE,
          ])}
        >
          <InertiaLink href={getPagesHref(E_PAGES.USER__NEW)}>
            <ButtonGeneral
              sx={{
                width: responsiveSize(140),
              }}
              label="新規作成"
            />
          </InertiaLink>
        </Closable>
      </Box>
      <UserList sx={{ marginTop: responsiveSpacing(4) }} />
    </Page>
  );
};
