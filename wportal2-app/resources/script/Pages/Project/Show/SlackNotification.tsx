import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { useIndexContext } from '@/script/Pages/Project/Show/Index';

interface IProps extends IPropsBase {}
export const SlackNotification: React.FC<IProps> = ({ sx }) => {
  const { trnProjectList } = useIndexContext();
  const trnProject = trnProjectList.first();

  const nodeItem = () => {
    if (!trnProject.trnProjectNotification?.length) {
      return <TypoText>なし</TypoText>;
    }

    return (
      <Box>
        {trnProject.trnProjectNotification?.map(notification => {
          return (
            <TypoText key={notification.id}>
              {notification.notificationValue}
            </TypoText>
          );
        })}
      </Box>
    );
  };

  return (
    <Box sx={sx}>
      <Box sx={{ display: 'flex' }}>
        <TypoText sx={{ minWidth: '200px', width: '200px' }}>
          Slack通知
        </TypoText>
        {nodeItem()}
      </Box>
    </Box>
  );
};
