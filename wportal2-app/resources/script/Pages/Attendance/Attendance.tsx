import * as React from 'react';
import { Box, IconButton } from '@mui/material';
import { router } from '@inertiajs/react';
import { IPropsBase } from '@/script/System/System';
import { Page } from '@/script/Pages/Page';
import { TypoH1 } from '@/script/Component/Typography/TypoH1';
import { E_PAGES, getPagesName } from '@/script/Enum/Server/App/EPages';
import { DateTime } from '@/script/Common/DateTime';
import { Myself } from '@/script/Pages/Attendance/Myself/Myself';
import { Action } from '@/script/Pages/Attendance/Action/Action';
import { Member } from '@/script/Pages/Attendance/Member/Member';
import { TypoH2 } from '@/script/Component/Typography/TypoH2';
import { responsiveSpacing } from '@/script/System/Responsive';
import { Icon } from '@/script/Component/Misc/Icon';
import { E_COLOR } from '@/script/Enum/EColor';
import { E_ICON } from '@/script/Enum/EIcon';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { useProgressContext } from '@/script/Provider/ProgressProvider';
import { useSnackbar } from '@/script/Provider/SnackbarProvider';
import { useIndexContext } from '@/script/Pages/Attendance/Index';
import { useEffect } from 'react';

interface IProps extends IPropsBase {}
export const Attendance: React.FC<IProps> = () => {
  const { onStart, onFinish } = useProgressContext();
  const { message } = useIndexContext();
  const snackbar = useSnackbar();

  /**
   * メッセージがあればスナックバー表示.
   */
  useEffect(() => {
    snackbar(message);
  }, [message]);

  /**
   * リロード処理
   */
  const handleReload = () => {
    // 再取得.
    router.reload({
      method: 'post',
      // @ts-expect-error inertia-preserve-state
      preserveState: true,
      preserveScroll: true,
      onStart,
      onFinish,
    });
  };

  return (
    <Page>
      <Box
        sx={{
          display: 'flex',
          justifyContent: 'space-between',
        }}
      >
        <TypoH1>
          {getPagesName(E_PAGES.ATTENDANCE)}({DateTime.now().toDateJP()})
        </TypoH1>
      </Box>
      <Myself
        sx={{
          marginTop: responsiveSpacing(4),
        }}
      />
      <Action sx={{ marginTop: responsiveSpacing(4) }} />
      <Box
        sx={{
          marginTop: responsiveSpacing(4),
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
        }}
      >
        <TypoH2>他メンバーの状況</TypoH2>
        <Box
          sx={{
            marginLeft: responsiveSpacing(2),
            display: 'flex',
            alignItems: 'center',
            columnGap: responsiveSpacing(2),
          }}
        >
          <IconButton
            sx={{
              padding: responsiveSpacing(1),
              border: '1px solid',
              borderColor: 'divider',
              backgroundColor: E_COLOR.WHITE,
            }}
            onClick={handleReload}
          >
            <Icon
              sx={{
                color: E_COLOR.PRIMARY_MAIN,
              }}
              icon={E_ICON.CACHED}
            />
          </IconButton>
          <TypoText>取得日時：{DateTime.now().toDateTime()}</TypoText>
        </Box>
      </Box>
      {/*<Office sx={{ marginTop: responsiveSpacing(2) }} />*/}
      <Member sx={{ marginTop: responsiveSpacing(4) }} />
    </Page>
  );
};
